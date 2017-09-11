<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Laravel;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Railt\Endpoint;
use Railt\Http\Request;
use Railt\Http\RequestInterface;

/**
 * Class RailtServiceProvider
 * @package Railt\Adapters\Laravel
 */
class RailtServiceProvider extends ServiceProvider
{
    /**
     * Dispatcher event names prefix
     */
    private const RAILT_EVENTS_PREFIX = 'railt:';

    /**
     * Local config file path
     */
    private const CONFIG_PATH = __DIR__ . '/../resources/config/railt.php';

    /**
     * Views path
     */
    private const VIEWS_PATH = __DIR__ . '/../resources/views';

    /**
     * @return void
     * @throws \Illuminate\Container\EntryNotFoundException
     * @throws \Railt\Parser\Exceptions\ParserException
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     */
    public function register(): void
    {
        $this->shareResources();
        $this->mergeConfigFrom(self::CONFIG_PATH, 'railt');
        $this->loadViewsFrom(self::VIEWS_PATH, 'railt');

        $this->registerRequestDependency();
        $this->registerConfigurationDependency();
        $this->registerEndpointDependency();
    }

    /**
     * @return void
     */
    private function shareResources(): void
    {
        $res = __DIR__ . '/../resources/';

        $this->publishes([
            self::CONFIG_PATH                           => config_path('railt.php'),
            $res . 'router/graphql.php'                 => base_path('routes/graphql.php'),
            $res . 'schema/schema.graphqls'             => resource_path('graphql/schema.graphqls'),
            $res . 'controllers/EchoController.php'     => app_path('GraphQL/Controllers/EchoController.php'),
            $res . 'controllers/UpperCaseDecorator.php' => app_path('GraphQL/Decorators/UpperCaseDecorator.php'),
        ], 'railt');
    }

    /**
     * @return void
     */
    private function registerRequestDependency(): void
    {
        $this->app->singleton(RequestInterface::class, function () {
            return Request::create($this->app->make(LaravelRequest::class));
        });
    }

    /**
     * @return void
     */
    private function registerConfigurationDependency(): void
    {
        $this->app->singleton(RailtConfiguration::class, function () {
            $config = $this->app->make(Repository::class);

            return new RailtConfiguration($config->get('railt', []));
        });
    }

    /**
     * @return void
     * @throws \Illuminate\Container\EntryNotFoundException
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     * @throws \Railt\Parser\Exceptions\ParserException
     */
    private function registerEndpointDependency(): void
    {
        $this->app->singleton(Endpoint::class, function () {
            $logger = $this->app->make(LoggerInterface::class);

            $endpoint = new Endpoint(new ContainerBridge($this->app), $logger);

            $this->registerEndpointDebugger($endpoint);
            $this->registerEndpointEvents($endpoint);

            return $endpoint;
        });
    }

    /**
     * @param Endpoint $endpoint
     */
    private function registerEndpointEvents(Endpoint $endpoint): void
    {
        // Railt event dispatcher
        $railt = $endpoint->getEvents();

        // Laravel event dispatcher
        $dispatcher = $this->app->make(Dispatcher::class);

        $railt->listen('*', function (string $name, $data) use ($dispatcher) {
            $dispatcher->dispatch(self::RAILT_EVENTS_PREFIX . $name, $data);
        });
    }

    /**
     * @param Endpoint $endpoint
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    private function registerEndpointDebugger(Endpoint $endpoint): void
    {
        $endpoint->debugMode(config('app.debug', false));
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        /** @var RailtConfiguration $config */
        $config = $this->app->make(RailtConfiguration::class);

        $config->registerRoutes($this->app->make(Registrar::class));
    }
}
