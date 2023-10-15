<?php

declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\CacheInterface;
use Railt\Contracts\Http\Factory\ErrorFactoryInterface;
use Railt\Contracts\Http\Factory\RequestFactoryInterface;
use Railt\Contracts\Http\Factory\ResponseFactoryInterface;
use Railt\Contracts\Http\Middleware\MiddlewareInterface;
use Railt\Executor\Webonyx\WebonyxExecutor;
use Railt\Extension\Router\RouterExtension;
use Railt\Foundation\Application;
use Railt\Foundation\ApplicationInterface;
use Railt\Foundation\Connection;
use Railt\Foundation\ConnectionInterface;
use Railt\Foundation\ExecutorInterface;
use Railt\Foundation\Extension\ExtensionInterface;
use Railt\Http\Factory\GraphQLErrorFactory;
use Railt\Http\Factory\GraphQLRequestFactory;
use Railt\Http\Factory\GraphQLResponseFactory;
use Railt\LaravelProvider\Compiler\DirectoryLoader;
use Railt\LaravelProvider\Controller\GraphQLRequestHandler;
use Railt\LaravelProvider\Controller\PlaygroundRequestHandler;
use Railt\SDL\Compiler;
use Railt\SDL\CompilerInterface;
use Railt\SDL\Config;
use Railt\SDL\Dictionary;

final class RailtServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!$this->app->configurationIsCached()) {
            $this->registerConfigs();
        }

        $this->registerGlobalServices();
        $this->registerExtensions();
    }

    private function registerConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/railt.php', 'railt');
    }

    private function registerGlobalServices(): void
    {
        $this->app->singleton(ErrorFactoryInterface::class, GraphQLErrorFactory::class);
        $this->app->singleton(RequestFactoryInterface::class, GraphQLRequestFactory::class);
        $this->app->singleton(ResponseFactoryInterface::class, GraphQLResponseFactory::class);
    }

    private function registerExtensions(): void
    {
        $this->registerRouterExtension();
    }

    private function registerRouterExtension(): void
    {
        $this->app->singleton(RouterExtension::class);
    }

    public function boot(ConfigRepository $config, Router $router): void
    {
        if ($this->app->runningInConsole()) {
            $this->configurePublishing();
        }

        $this->registerConfigAwareCompilers($config);
        $this->registerConfigAwareApplications($config, $router);
        $this->registerConfigAwarePlaygrounds($config, $router);
    }

    /**
     * Configure publishing for the package.
     */
    private function configurePublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/config/railt.php' => $this->app->configPath('railt.php'),
        ], ['railt', 'railt-config']);

        $this->publishes([
            __DIR__ . '/../resources/assets/schema.graphqls'
                => $this->app->resourcePath('graphql/schema.graphqls'),
            __DIR__ . '/../resources/assets/ExampleController.php'
                => \app_path('Http/Controllers/GraphQL/ExampleController.php'),
        ], ['railt', 'railt-assets']);
    }

    private function registerConfigAwareCompilers(ConfigRepository $repository): void
    {
        /**
         * @var array{
         *     cache: non-empty-string|null,
         *     spec: non-empty-string,
         *     generate: array{
         *         query: non-empty-string|null,
         *         mutation: non-empty-string|null,
         *         subscription: non-empty-string|null
         *     },
         *     cast: array{
         *         int_to_float: bool,
         *         scalar_to_string: bool
         *     },
         *     extract: array{
         *         nullable: bool,
         *         list: bool
         *     },
         *     autoload: list<non-empty-string>,
         * } $config
         */
        foreach ((array)$repository->get('railt.compilers', []) as $name => $config) {
            $this->app->singleton("railt.$name.compiler", function () use ($config): Compiler {
                $types = new Dictionary();

                if (isset($config['types']) && $this->app->bound($config['types'])) {
                    $types = $this->app->make($config['types']);
                }

                $compiler = new Compiler(
                    config: new Config(
                        spec: Config\Specification::from($config['spec'] ?? 'railt'),
                        generateSchema: new Config\GenerateSchema(
                            queryTypeName: $config['generate']['query'] ?? null,
                            mutationTypeName: $config['generate']['mutation'] ?? null,
                            subscriptionTypeName: $config['generate']['subscription'] ?? null,
                        ),
                        castIntToFloat: (bool)($config['cast']['int_to_float'] ?? true),
                        castScalarToString: (bool)($config['cast']['scalar_to_string'] ?? true),
                        castNullableTypeToDefaultValue: (bool)($config['extract']['nullable'] ?? true),
                        castListTypeToDefaultValue: (bool)($config['extract']['list'] ?? true)
                    ), cache: $this->createCache($config['cache'] ?? null), types: $types,
                );

                foreach ((array)($config['autoload'] ?? []) as $directory) {
                    $compiler->addLoader(new DirectoryLoader($directory));
                }

                return $compiler;
            });

            if (!$this->app->bound(Compiler::class)) {
                $this->app->alias("railt.$name.compiler", Compiler::class);
            }

            if (!$this->app->bound(CompilerInterface::class)) {
                $this->app->alias("railt.$name.compiler", CompilerInterface::class);
            }
        }
    }

    private function createCache(?string $name): ?CacheInterface
    {
        if (!$this->app->bound('cache')) {
            return null;
        }

        /** @var CacheManager $manager */
        $manager = $this->app->get('cache');

        if (!$manager instanceof CacheManager) {
            return null;
        }

        $driver = $manager->driver($name);

        if ($driver instanceof CacheInterface) {
            return $driver;
        }

        return null;
    }

    private function registerConfigAwareApplications(ConfigRepository $repository, Router $router): void
    {
        /**
         * @var array{
         *     route: non-empty-string,
         *     methods: list<non-empty-string>,
         *     schema: non-empty-string,
         *     variables: array<non-empty-string, mixed>,
         *     executor: non-empty-string|null,
         *     compiler: non-empty-string|null,
         *     middleware: list<non-empty-string|class-string<MiddlewareInterface>>,
         *     extensions: list<non-empty-string|class-string<ExtensionInterface>>
         * } $app
         */
        foreach ((array)$repository->get('railt.endpoints', []) as $name => $app) {
            $controller = "railt.$name.graphql_controller";
            $route = "railt.$name";

            //
            // Create Railt Application
            //

            $this->app->singleton("railt.$name.application", function () use ($app): object {
                return new Application(
                    executor: match (true) {
                        isset($app['executor'])
                            => $this->app->make($app['executor']),
                        $this->app->bound(ExecutorInterface::class)
                            => $this->app->make(ExecutorInterface::class),
                        default => $this->app->make(WebonyxExecutor::class),
                    },
                    compiler: match (true) {
                        isset($app['compiler'])
                            => $this->app->bound($app['compiler'])
                                ? $this->app->make($app['compiler'])
                                : $this->app->make("railt.{$app['compiler']}.compiler"),
                        $this->app->bound(CompilerInterface::class)
                            => $this->app->make(CompilerInterface::class),
                        default => $this->app->make(Compiler::class),
                    },
                    extensions: collect($app['extensions'] ?? [])
                        ->map(fn (string $name): object => $this->app->make($name)),
                    dispatcher: $this->app->bound('events')
                        ? new EventDispatcherAdapter($this->app->make('events'))
                        : null,
                );
            });

            if (!$this->app->bound(Application::class)) {
                $this->app->alias("railt.$name.application", Application::class);
            }

            if (!$this->app->bound(ApplicationInterface::class)) {
                $this->app->alias("railt.$name.application", ApplicationInterface::class);
            }

            //
            // Create Railt Connection
            //

            $this->app->singleton("railt.$name.connection", function () use ($name, $app): object {
                /** @var Application $application */
                $application = $this->app->make("railt.$name.application");

                assert($application instanceof Application);

                return $application->connect(
                    schema: new \SplFileInfo($app['schema']),
                    variables: (array)($app['variables'] ?? []),
                );
            });

            if (!$this->app->bound(Connection::class)) {
                $this->app->alias("railt.$name.connection", Connection::class);
            }

            if (!$this->app->bound(ConnectionInterface::class)) {
                $this->app->alias("railt.$name.connection", ConnectionInterface::class);
            }

            //
            // Create Railt GraphQL Request Handler
            //

            $this->app->singleton($controller, function () use ($name, $app): object {
                return new GraphQLRequestHandler(
                    connection: $this->app->make("railt.$name.connection"),
                    requests: $this->app->make(RequestFactoryInterface::class),
                );
            });

            if ($this->app->routesAreCached()) {
                continue;
            }

            $router->match(
                methods: (array)($app['methods'] ?? ['post']),
                uri: $app['route'],
                action: "$controller@__invoke",
            )
                ->middleware((array)($config['middleware'] ?? []))
                ->name($route);
        }
    }

    /**
     * Registers the controllers and routes for the playground.
     */
    private function registerConfigAwarePlaygrounds(ConfigRepository $repository, Router $router): void
    {
        /**
         * @var array{
         *     route: non-empty-string,
         *     endpoint: non-empty-string,
         *     methods: list<non-empty-string>,
         *     middleware: list<non-empty-string>,
         *     headers: array<non-empty-string, non-empty-string>
         * } $config
         */
        foreach ((array)$repository->get('railt.playground', []) as $name => $config) {
            $controller = "railt.$name.graphiql_controller";
            $route = "railt.$name.playground";

            $this->app->singleton($controller, function () use ($repository, $config): object {
                $uri = $repository->get("railt.endpoints.{$config['endpoint']}.route");

                if (!\is_string($uri)) {
                    $message = \vsprintf('GraphQL endpoint [railt.endpoint.%s] not found.', [
                        $config['endpoint'] ?? '<unknown>',
                    ]);

                    throw new \InvalidArgumentException($message);
                }

                return new PlaygroundRequestHandler($uri, (array)$config['headers']);
            });

            if ($this->app->routesAreCached()) {
                continue;
            }

            $router->match(
                methods: (array)($config['methods'] ?? ['get']),
                uri: $config['route'],
                action: "$controller@__invoke",
            )
                ->middleware((array)($config['middleware'] ?? []))
                ->name($route);
        }
    }
}
