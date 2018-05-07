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
use Railt\Http\Provider\IlluminateProvider;
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
        $config = new Config($repository->get('railt'));

        // Register a configuration
        $this->app->instance(Config::class, $config);

        // Cache
        // $this->registerCacheDriver();

        // Http
        $this->registerRequest();

        // Add endpoint
        $this->createRoute($config, $this->app->make(Registrar::class));
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
        $this->app->bind(RequestInterface::class, function (): RequestInterface {
            $provider = new IlluminateProvider($this->app->make(LaravelRequest::class));
            return new Request($provider);
        });

        $this->app->alias(RequestInterface::class, Request::class);
    }

    /**
     * @param Config $config
     * @param Registrar $registrar
     * @throws \InvalidArgumentException
     */
    private function createRoute(Config $config, Registrar $registrar): void
    {
        foreach ($config->getEndpoints() as $endpoint) {
            $registrar->match($endpoint->getMethods(), $endpoint->getUri(), $endpoint->getControllerAndAction())
                ->name($endpoint->getName())
                ->middleware($endpoint->getMiddleware());
        }
    }
}
