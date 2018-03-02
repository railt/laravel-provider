<?php
/**
 * This file is part of railt.org package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Config\Repository;
use Railt\LaravelProvider\Config\Endpoint;

/**
 * Class Config
 */
class Config extends Repository
{
    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return (bool)$this->get('debug', false);
    }

    /**
     * @return iterable|Endpoint[]
     */
    public function getEndpoints(): iterable
    {
        foreach ((array)$this->get('endpoints', []) as $uri => $value) {
            yield new Endpoint(\is_int($uri) ? $value : \array_merge(['uri' => $uri], $value));
        }
    }

    /**
     * @param string $name
     * @return Endpoint
     * @throws \OutOfRangeException
     */
    public function getEndpoint(string $name): Endpoint
    {
        foreach ($this->getEndpoints() as $endpoint) {
            if ($endpoint->getName() === $name) {
                return $endpoint;
            }
        }

        throw new \OutOfRangeException('Could not find an available endpoint with name "' . $name . '"');
    }
}
