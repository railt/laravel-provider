<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Laravel;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class RailtConfiguration
 * @package Railt\Adapters\Laravel
 */
class RailtConfiguration extends Repository
{
    /**
     * GraphiQL Action
     */
    private const GRAPHIQL_ROUTE_NAME = 'graphiql.a';

    /**
     * @param Registrar $registrar
     */
    public function registerRoutes(Registrar $registrar)
    {
        $this->registerRoutesCollection($registrar, 'endpoints', '');
        $this->registerRoutesCollection($registrar, 'graphiql', self::GRAPHIQL_ROUTE_NAME);
    }

    /**
     * @param Registrar $registrar
     * @param string $key
     * @param string $name
     */
    private function registerRoutesCollection(Registrar $registrar, string $key, string $name = '')
    {
        $prefix = $this->getRoutesPrefix();

        foreach ((array)$this->get($key, []) as $id => $config) {
            if (! ($config['enabled'] ?? true)) {
                continue;
            }

            $this->registerRoute($registrar, $prefix . $name . (string)$id, $config);
        }
    }

    /**
     * @return string
     */
    private function getRoutesPrefix(): string
    {
        return $this->get('prefix', 'railt.');
    }

    /**
     * @param Registrar $registrar
     * @param string $key
     * @param array $config
     */
    private function registerRoute(Registrar $registrar, string $key, array $config)
    {
        $methods = Arr::get($config, 'methods', ['GET', 'POST']);
        $uri     = $config['uri'] ?? '/graphql';

        $registrar->match($methods, $uri, $config['uses'] ?? null)
            ->name($key)
            ->middleware($config['middleware'] ?? []);
    }

    /**
     * @param string $action
     * @param string $prefix
     * @return string
     */
    private function getNodeName(string $action, string $prefix = ''): string
    {
        $default = $action ?? $this->get('default', 'default');

        return Str::replaceFirst($this->getRoutesPrefix() . $prefix, '', $default);
    }

    /**
     * @param string|null $action
     * @param string $prefix
     * @return array
     */
    private function getData(string $action, string $prefix = ''): array
    {
        $key = $this->getNodeName($action, $prefix);

        return (array)$this->get('endpoints.' . $key, []);
    }

    /**
     * @param string $action
     * @return string
     */
    public function getSchemaPathname(string $action): string
    {
        return (string)Arr::get($this->getData($action), 'schema');
    }

    /**
     * @param string $action
     * @return string
     */
    public function getRouterPathname(string $action): string
    {
        return (string)Arr::get($this->getData($action), 'router');
    }

    /**
     * @param string $action
     * @return array
     */
    public function getAutoloadPaths(string $action): array
    {
        return (array)Arr::get($this->getData($action), 'autoload');
    }

    /**
     * @param string $action
     * @return string
     */
    public function getRouteForAction(string $action): string
    {
        $key = $this->getNodeName($action, self::GRAPHIQL_ROUTE_NAME);
        $key = $this->get('graphiql.' . $key, [])['endpoint'] ?? 'default';

        return route($this->getRoutesPrefix() . $key);
    }
}
