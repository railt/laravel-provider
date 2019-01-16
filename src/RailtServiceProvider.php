<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;
use Railt\Discovery\Discovery;
use Railt\Foundation\Application;
use Railt\Foundation\ApplicationInterface;
use Railt\Foundation\Config\Composer;
use Railt\Http\Factory;
use Railt\Http\Provider\DataProvider;
use Railt\Http\Provider\IlluminateProvider;
use Railt\Http\Provider\ProviderInterface;
use Railt\Http\Request;
use Railt\Http\RequestInterface;
use Railt\Storage\Drivers\Psr16Storage;
use Railt\Storage\Storage;

/**
 * Class RailtServiceProvider
 * @package Railt\LaravelProvider
 */
class RailtServiceProvider extends ServiceProvider
{
    /**
     * Local resources directory.
     */
    private const RES_PATH = __DIR__ . '/../resources';

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
     */
    public function register(): void
    {
        $this->shareResources();
        $this->mergeConfigFrom(self::CONFIG_PATH, 'railt');
        $this->loadViewsFrom(self::VIEWS_PATH, 'railt');
    }

    /**
     * @return void
     */
    private function shareResources(): void
    {
        $publishes = [
            self::CONFIG_PATH                                  => \config_path('railt.php'),
            self::RES_PATH . '/schema/schema.graphqls'         => \resource_path('graphql/schema.graphqls'),
            self::RES_PATH . '/controllers/EchoController.php' => \app_path('GraphQL/Controllers/EchoController.php'),
        ];

        $this->publishes($publishes, 'railt');
    }

    /**
     * @param Repository $repository
     * @throws \InvalidArgumentException
     * @throws \Railt\SDL\Exceptions\CompilerException
     * @throws \OutOfBoundsException
     * @throws \LogicException
     */
    public function boot(Repository $repository): void
    {
        $config = $repository->get('railt');

        $this->registerCacheDriver();
        $this->registerRequest();
        $this->registerApplication($config['debug'] ?? false);

        // Add endpoint
        $this->createRoute($config, $this->app->make(Registrar::class));
    }

    /**
     * @param bool $debug
     */
    private function registerApplication(bool $debug): void
    {
        $this->app->bind(ApplicationInterface::class, function() use ($debug): ApplicationInterface {
            $app = new Application($debug, $this->app);
            $app->configure(new Composer(Discovery::auto()));

            return $app;
        });
    }

    /**
     * Initialize PSR-16 Cache driver.
     *
     * @return void
     */
    private function registerCacheDriver(): void
    {
        $this->app->singleton(Storage::class, function (): Storage {
            return new Psr16Storage($this->app->make(Cache::class));
        });
    }

    /**
     * @return void
     * @throws \LogicException
     */
    private function registerRequest(): void
    {
        $this->app->bind(ProviderInterface::class, function(): ProviderInterface {
            $laravel = $this->app->make(LaravelRequest::class);

            $provider = new DataProvider($laravel->query->all(), $laravel->request->all());
            $provider->withContentType($laravel->getContentType());
            $provider->withBody($laravel->getContent());

            return $provider;
        });

        $this->app->alias(RequestInterface::class, Request::class);
    }

    /**
     * @param Registrar $config
     * @throws \InvalidArgumentException
     */
    private function createRoute(array $config, Registrar $registrar): void
    {
        // GET, POST, PATCH, etc
        $method = $config['methods'] ?? ['POST'];

        // Endpoint URI
        $uri = $config['uri'] ?? '/graphql';

        // Controller and Action
        $uses = $config['uses'] ?? GraphQLController::class . '@handle';

        $route = $registrar->match($method, $uri, $uses)
            ->name(($config['prefix'] ?? '') . 'graphql')
            ->middleware($config['middleware'] ?? []);
    }
}
