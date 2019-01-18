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
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\LaravelProvider\Config;
use Railt\LaravelProvider\Controllers\GraphQLController;

/**
 * Class Endpoint
 */
class Endpoint
{
    /**
     * @var string
     */
    private const CONFIG_SCHEMA = 'schema';

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
    private const CONFIG_ROUTE_NAME = 'name';

    /**
     * @var string
     */
    private const CONFIG_ROUTE_METHODS = 'methods';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $schema;

    /**
     * @var array|string[]
     */
    private $methods;

    /**
     * @var array|string[]
     */
    private $middleware;

    /**
     * @var Config
     */
    private $parent;

    /**
     * @var string
     */
    private $original;

    /**
     * Endpoint constructor.
     * @param Config $parent
     * @param string $name
     * @param array $config
     */
    public function __construct(Config $parent, string $name, array $config)
    {
        $this->original = $name;
        $this->parent = $parent;

        $this->name = (string)($config[self::CONFIG_ROUTE_NAME] ?? 'railt.' . $name);
        $this->methods = (array)($config[self::CONFIG_ROUTE_METHODS] ?? ['POST']);
        $this->middleware = (array)($config[self::CONFIG_ROUTE_MIDDLEWARE] ?? []);
        $this->schema = (string)($config[self::CONFIG_SCHEMA] ?? \resource_path('graphql/schema.graphqls'));
        $this->route = (string)($config[self::CONFIG_ROUTE] ?? 'graphql');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->original;
    }

    /**
     * @return Readable
     * @throws \Railt\Io\Exception\NotReadableException
     */
    public function getSchema(): Readable
    {
        return File::fromPathname($this->schema);
    }

    /**
     * @param Registrar $registrar
     */
    public function register(Registrar $registrar): void
    {
        $registrar->match($this->methods, $this->route, GraphQLController::class . '@graphqlAction')
            ->middleware($this->middleware)
            ->name($this->getRouteName());
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->name;
    }
}

