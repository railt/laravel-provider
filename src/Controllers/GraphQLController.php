<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Laravel\Controllers;

use Illuminate\Http\Request;
use Railt\Foundation\Endpoint;
use Railt\Http\RequestInterface;
use Railt\Adapters\Laravel\RailtConfiguration;
use Railt\Parser\File;
use Railt\Routing\Router;

/**
 * Class GraphQLController
 * @package Railt\Adapters\Laravel\Controllers
 */
class GraphQLController
{
    /**
     * @var RailtConfiguration
     */
    private $config;

    /**
     * @var string
     */
    private $action;

    /**
     * GraphQLController constructor.
     * @param RailtConfiguration $config
     */
    public function __construct(RailtConfiguration $config, Request $request)
    {
        $this->config = $config;
        $this->action = $request->route()->getName();
    }

    /**
     * @param Endpoint $endpoint
     * @param RequestInterface $request
     * @return array
     * @throws \LogicException
     * @throws \Railt\Parser\Exceptions\UnrecognizedTokenException
     * @throws \Railt\Parser\Exceptions\NotReadableException
     * @throws \Railt\Reflection\Exceptions\TypeConflictException
     * @throws \Railt\Reflection\Exceptions\UnrecognizedNodeException
     */
    public function index(Endpoint $endpoint, RequestInterface $request): array
    {
        $this->registerRouter($endpoint->getRouter());

        $schema = File::path($this->getSchemaPathname());

        $response = $endpoint->request($schema, $request);

        return $response->toArray();
    }

    /**
     * @return string
     * @throws \LogicException
     */
    private function getSchemaPathname(): string
    {
        return $this->config->getSchemaFile($this->action);
    }

    /**
     * @param Router $router
     * @throws \LogicException
     */
    private function registerRouter(Router $router): void
    {
        $callee = function(string $file) use ($router) {
            if (!is_file($file)) {
                throw new \InvalidArgumentException('Railt router file "' . $file . '" not exists.');
            }

            require $file;
        };

        $callee->call(new class {},  $this->config->getRoutesFile($this->action));
    }
}
