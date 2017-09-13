<?php
/**
 * This file is part of laravel-adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Railt\Container\ContainerInterface;
use Illuminate\Contracts\Container\Container;
use Railt\Container\RegistrableInterface;

/**
 * Class ContainerBridge
 * @package Railt\LaravelProvider
 */
class ContainerBridge implements ContainerInterface
{
    /**
     * @var Container
     */
    private $laravel;

    /**
     * ContainerBridge constructor.
     * @param Container $laravel
     */
    public function __construct(Container $laravel)
    {
        $this->laravel = $laravel;
    }

    /**
     * @param callable $action
     * @param array $params
     * @return mixed
     */
    public function call(callable $action, array $params = [])
    {
        return $this->laravel->call($action, $params);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->laravel->make($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->laravel->bound($id);
    }

    public function factory(string $key, $value): RegistrableInterface
    {
        $this->laravel->bind($key, $value);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return RegistrableInterface
     */
    public function singleton(string $key, $value): RegistrableInterface
    {
        $this->laravel->singleton($key, $value);

        return $this;
    }
}
