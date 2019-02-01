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
use Railt\Foundation\Application;
use Railt\Foundation\ApplicationInterface;
use Railt\Storage\Drivers\Psr16Storage;
use Railt\Storage\Storage;

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
        $this->app->singleton(Storage::class, function (): Storage {
            return new Psr16Storage($this->app->make(Cache::class));
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
     */
    public function boot(Config $config, Registrar $registrar)
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
     */
    private function registerCommands(): void
    {
        $railt = $this->app->make(ApplicationInterface::class);
    }
}
