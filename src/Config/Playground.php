<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Config;

use Illuminate\Contracts\Routing\Registrar;
use Railt\LaravelProvider\Config;
use Railt\LaravelProvider\Controllers\GraphQLController;

/**
 * Class Playground
 */
class Playground
{
    /**
     * @var string
     */
    private const CONFIG_ENABLED = 'enabled';

    /**
     * @var string
     */
    private const CONFIG_ROUTE = 'route';

    /**
     * @var string
     */
    private const CONFIG_ROUTE_MIDDLEWARE = 'middleware';

    /**
     * @var string
     */
    private const CONFIG_SETTINGS = 'settings';

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array|string[]
     */
    private $middleware;

    /**
     * @var Config
     */
    private $parent;

    /**
     * @var array
     */
    private $settings;

    /**
     * GraphiQL constructor.
     * @param Config $parent
     * @param array $config
     */
    public function __construct(Config $parent, array $config)
    {
        $this->parent = $parent;

        $this->enabled = $config[self::CONFIG_ENABLED] ?? $parent->isDebug();
        $this->route = $config[self::CONFIG_ROUTE] ?? '/graphql/playground';
        $this->middleware = $config[self::CONFIG_ROUTE_MIDDLEWARE] ?? [];
        $this->settings = $config[self::CONFIG_SETTINGS] ?? [];
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param Registrar $registrar
     */
    public function register(Registrar $registrar): void
    {
        if ($this->enabled) {
            $registrar->get($this->route, GraphQLController::class . '@playgroundAction')
                ->middleware($this->middleware)
                ->name('railt.playground');
        }
    }
}
