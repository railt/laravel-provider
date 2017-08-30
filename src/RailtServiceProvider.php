<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Laravel;

use Psr\Log\LoggerInterface;
use Railt\Foundation\Endpoint;
use Railt\Http\Request;
use Railt\Http\RequestInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request as LaravelRequest;

/**
 * Class RailtServiceProvider
 * @package Railt\Adapters\Laravel
 */
class RailtServiceProvider extends ServiceProvider
{
    /**
     * Local config file path
     */
    private const PACKAGE_CONFIG_PATH = __DIR__ . '/../resources/railt.php';
    private const PACKAGE_ROUTER_PATH = __DIR__ . '/../resources/routes.php';
    private const PACKAGE_SCHEMA_PATH = __DIR__ . '/../resources/schema.graphqls';

    /**
     * @return void
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     * @throws \Railt\Parser\Exceptions\ParserException
     */
    public function register(): void
    {
        $this->shareResources();
        $this->mergeConfigFrom(self::PACKAGE_CONFIG_PATH, 'railt');

        $this->registerRequestDependency();
        $this->registerConfigurationDependency();
        $this->registerEndpointDependency();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        /**
         * Register routes
         */
        $this->registerRoutes(
            $this->app->make(Registrar::class),
            $this->app->make(RailtConfiguration::class)
        );
    }

    /**
     * @param Registrar $router
     * @param RailtConfiguration $config
     */
    private function registerRoutes(Registrar $router, RailtConfiguration $config): void
    {
        foreach ($config->getRoutes() as $url => $routeConfig) {
            $router->match(['GET', 'POST'], $url, $routeConfig);
        }
    }

    /**
     * @return void
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     * @throws \Railt\Parser\Exceptions\ParserException
     */
    private function registerEndpointDependency(): void
    {
        $this->app->singleton(Endpoint::class, function () {
            return new Endpoint(
                new ContainerBridge($this->app),
                $this->app->make(LoggerInterface::class)
            );
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
    private function shareResources(): void
    {
        $this->publishes([
            self::PACKAGE_CONFIG_PATH => config_path('railt.php'),
            self::PACKAGE_ROUTER_PATH => base_path('routes/graphql.php'),
            self::PACKAGE_SCHEMA_PATH => resource_path('graphql/schema.graphqls'),
        ]);
    }
}
