<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Laravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Railt\Adapters\Laravel\Controllers\GraphQLController;

/**
 * Class RailtConfiguration
 * @package Railt\Adapters\Laravel
 */
class RailtConfiguration extends Repository
{
    /**
     * @return iterable
     */
    public function getRoutes(): iterable
    {
        $prefix = $this->getRoutesPrefix();

        $routes = (array)$this->get('routes', []);

        foreach ($routes as $name => $config) {
            $name = is_int($name) ? 'default' : $name;

            yield $config['url'] => [
                'uses' => Arr::get($config, 'uses', GraphQLController::class . '@index'),
                'as'   => $prefix . $name,
            ];
        }
    }

    /**
     * @return string
     */
    public function getRoutesPrefix(): string
    {
        return $this->get('config.routes_prefix', 'railt.');
    }

    /**
     * @param string $actionName
     * @return string
     */
    private function getConfigName(string $actionName): string
    {
        return Str::replaceFirst($this->getRoutesPrefix(), '', $actionName);
    }

    /**
     * @param string $actionName
     * @param string $key
     * @return mixed
     * @throws \LogicException
     */
    private function getByAction(string $actionName, string $key)
    {
        $config = $this->getConfigName($actionName);

        $value = $this->get($config . '.' . $key);

        if ($value === null) {
            throw new \LogicException('"' . $config . '.'  .$key . '" missing in railt configuration file.');
        }

        return $value;
    }

    /**
     * @param string $actionName
     * @return string
     * @throws \LogicException
     */
    public function getRoutesFile(string $actionName): string
    {
        return (string)$this->getByAction($actionName, 'router');
    }

    /**
     * @param string $actionName
     * @return string
     * @throws \LogicException
     */
    public function getSchemaFile(string $actionName): string
    {
        return (string)$this->getByAction($actionName, 'schema');
    }
}
