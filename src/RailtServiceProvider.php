<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\CacheInterface;
use Railt\Foundation\Application;
use Railt\Foundation\ApplicationInterface;
use Symfony\Component\Console\Command\Command;

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
    private function registerConfiguration(): void
    {
        $this->app->singleton(Config::class, function ($app) {
            $repository = $app->make(Repository::class);

            return new Config($repository->get(Config::ROOT_NODE, []));
        });
    }

    /**
     * @return void
     */
    private function registerStorage(): void
    {
        $this->app->singleton(CacheInterface::class, function (): CacheInterface {
            return $this->app->make(Cache::class);
        });
    }

    /**
     * @return void
     */
    private function registerApplication(): void
    {
        $this->app->bind(ApplicationInterface::class, function ($app): ApplicationInterface {
            $config = $app->make(Config::class);

            return new Application($config->isDebug(), $this->app);
        });
    }

    /**
     * @param Config $config
     * @param Registrar $registrar
     * @throws \Railt\Container\Exception\ContainerInvocationException
     * @throws \Railt\Container\Exception\ContainerResolutionException
     * @throws \Railt\Container\Exception\ParameterResolutionException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    public function boot(Config $config, Registrar $registrar): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        $this->registerRoutes($config, $registrar);
    }

    /**
     * @param Config $config
     * @param Registrar $registrar
     */
    private function registerRoutes(Config $config, Registrar $registrar)
    {
        $config->register($registrar);
    }

    /**
     * @return void
     * @throws \Railt\Container\Exception\ContainerInvocationException
     * @throws \Railt\Container\Exception\ContainerResolutionException
     * @throws \Railt\Container\Exception\ParameterResolutionException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
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
}
