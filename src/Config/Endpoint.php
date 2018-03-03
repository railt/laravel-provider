<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Config;

use Illuminate\Config\Repository;
use Railt\Io\File;
use Railt\Io\Readable;

/**
 * Class Endpoint
 */
class Endpoint extends Repository
{
    /**
     * @return Readable
     * @throws \InvalidArgumentException
     */
    public function getSchema(): Readable
    {
        return File::fromPathname(\tap($this->get('schema', ''), function (string $path): void {
            if (! $path) {
                throw new \InvalidArgumentException('The "schema" configuration argument required');
            }
        }));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $prefix = (string)$this->get('prefix', 'railt.');

        return $prefix . \str_replace('/', '.', $this->getUri());
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return (string)$this->get('uri', 'graphql');
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getControllerAndAction(): string
    {
        return \tap((string)$this->get('uses', ''), function (string $value): void {
            if (! $value) {
                throw new \InvalidArgumentException('The "uses" configuration argument required');
            }
        });
    }

    /**
     * @return array|string[]
     */
    public function getMethods(): array
    {
        $exceptHeadAndOptions = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        return (array)$this->get('methods', $exceptHeadAndOptions);
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return (array)$this->get('middleware', []);
    }

    /**
     * @return array|string[]
     */
    public function getExtensions(): array
    {
        return (array)$this->get('extensions');
    }
}
