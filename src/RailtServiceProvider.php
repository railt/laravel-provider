<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Railt\Foundation\Application;
use Psr\SimpleCache\CacheInterface;
use Illuminate\Support\ServiceProvider;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Railt\Foundation\ApplicationInterface;
use Railt\Extension\Normalization\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\Console\Command\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Railt\Extension\Normalization\NormalizerInterface;
use Railt\LaravelProvider\Normalization\ArrayableNormalizer;
use Railt\LaravelProvider\Normalization\RenderableNormalizer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Railt\Container\Exception\ContainerInvocationException;
use Railt\Container\Exception\ContainerResolutionException;
use Railt\Container\Exception\ParameterResolutionException;

/**
 * Class RailtServiceProvider
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
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->shareResources();

        $this->mergeConfigFrom(self::CONFIG_PATH, 'railt');
        $this->loadViewsFrom(self::VIEWS_PATH, 'railt');

        $this->registerStorage();
        $this->registerApplication();
        $this->registerConfiguration();
    }

    /**
     * @return void
     * @throws BindingResolutionException
     */
    private function shareResources(): void
    {
        $views = self::VIEWS_PATH;
        $configs = self::CONFIG_PATH;
        $resources = self::RES_PATH . '/schema/schema.graphqls';
        $controllers = self::RES_PATH . '/controllers/EchoController.php';

        $this->publishes([
            $views       => \resource_path('views/vendor/railt'),
            $configs     => \config_path('railt.php'),
            $resources   => \resource_path('graphql/schema.graphqls'),
            $controllers => \app_path('Http/Controllers/GraphQL/EchoController.php'),
        ], 'railt');

        $this->loadViewsFrom(self::VIEWS_PATH, 'railt');
    }

    /**
     * @return void
     */
    private function registerStorage(): void
    {
        if (! $this->app->bound(CacheInterface::class)) {
            $this->app->singleton(CacheInterface::class, function (Container $app): CacheInterface {
                $config = $app->make(Config::class);

                return $config->isCacheEnabled()
                    ? $this->app->make(Cache::class)
                    : new ArrayCachePool();
            });
        }
    }

    /**
     * @return void
     */
    private function registerApplication(): void
    {
        $this->app->bind(ApplicationInterface::class, function (Container $app): ApplicationInterface {
            $config = $app->make(Config::class);

            return $this->extend(new Application($config->isDebug(), $this->app));
        });
    }

    /**
     * @param Application $app
     * @return Application
     * @throws ContainerInvocationException
     * @throws ContainerResolutionException
     * @throws ParameterResolutionException
     */
    private function extend(Application $app): Application
    {
        /** @var Factory $factory */
        $factory = $app->make(NormalizerInterface::class);

        $factory->prepend(new ArrayableNormalizer());
        $factory->prepend(new RenderableNormalizer());

        return $app;
    }

    /**
     * @return void
     */
    private function registerConfiguration(): void
    {
        $this->app->singleton(Config::class, static function (Container $app) {
            $repository = $app->make(Repository::class);

            return new Config($repository->get(Config::ROOT_NODE, []));
        });
    }

    /**
     * @param Config $config
     * @param Registrar $registrar
     * @throws ContainerInvocationException
     * @throws ContainerResolutionException
     * @throws ParameterResolutionException
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public function boot(Config $config, Registrar $registrar): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        $this->registerRoutes($config, $registrar);
    }

    /**
     * @return void
     * @throws ContainerInvocationException
     * @throws ContainerResolutionException
     * @throws ParameterResolutionException
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    private function registerCommands(): void
    {
        /** @var ApplicationInterface $railt */
        $railt = $this->app->make(ApplicationInterface::class);

        if ($railt instanceof Application) {
            foreach ($railt->getCommands() as $command) {
                /** @var Command $instance */
                $instance = $railt->make($command);
                $instance->setName('railt:' . $instance->getName());

                $this->app->instance($command, $instance);
            }

            $this->commands($railt->getCommands());
        }
    }

    /**
     * @param Config $config
     * @param Registrar $registrar
     */
    private function registerRoutes(Config $config, Registrar $registrar): void
    {
        $config->register($registrar);
    }
}
