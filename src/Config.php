<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Support\Str;
use Railt\LaravelProvider\Config\Endpoint;
use Illuminate\Contracts\Routing\Registrar;
use Railt\LaravelProvider\Config\Playground;

/**
 * Class Config
 */
class Config
{
    /**
     * @var string
     */
    public const ROOT_NODE = 'railt';

    /**
     * @var string
     */
    public const DEBUG_NODE = 'debug';

    /**
     * @var string
     */
    public const CACHE_IS_ENABLED = 'cache';

    /**
     * @var string
     */
    private const ENDPOINTS_NODE = 'endpoints';

    /**
     * @var string
     */
    private const PLAYGROUND_NODE = 'playground';

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var array|Endpoint[]
     */
    private $endpoints = [];

    /**
     * @var Playground
     */
    private $graphiql;

    /**
     * @var bool
     */
    private $cache;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->debug = $config[static::DEBUG_NODE] ?? false;

        foreach ($config[static::ENDPOINTS_NODE] ?? [] as $name => $endpoint) {
            $this->endpoints[$name] = new Endpoint($this, $name, $endpoint);
        }

        $this->graphiql = new Playground($this, (array)($config[static::PLAYGROUND_NODE] ?? []));

        $this->cache = $config[static::CACHE_IS_ENABLED] ?? ! $this->debug;
    }

    /**
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        if (\is_string($this->cache) && Str::lower($this->cache) === 'false') {
            return false;
        }

        return (bool)$this->cache;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        if (\is_string($this->debug) && Str::lower($this->debug) === 'false') {
            return false;
        }

        return (bool)$this->debug;
    }

    /**
     * @param Registrar $registrar
     */
    public function register(Registrar $registrar): void
    {
        foreach ($this->getEndpoints() as $endpoint) {
            $endpoint->register($registrar);
        }

        $this->getPlayground()->register($registrar);
    }

    /**
     * @return array|Endpoint[]
     */
    public function getEndpoints(): array
    {
        return $this->endpoints;
    }

    /**
     * @return Playground
     */
    public function getPlayground(): Playground
    {
        return $this->graphiql;
    }

    /**
     * @param string $name
     * @return Endpoint|null
     */
    public function findByRouteName(string $name): ?Endpoint
    {
        foreach ($this->endpoints as $endpoint) {
            if ($endpoint->getRouteName() === $name) {
                return $endpoint;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return Endpoint|null
     */
    public function findByName(string $name): ?Endpoint
    {
        return $this->endpoints[$name] ?? null;
    }
}
