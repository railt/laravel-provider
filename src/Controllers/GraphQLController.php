<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Controllers;

use Illuminate\Contracts\View\View;
use Railt\Endpoint;
use Railt\Parser\File;
use Railt\Reflection\Autoloader;
use Railt\Routing\Router;
use Illuminate\Http\Request;
use Railt\Http\RequestInterface;
use Railt\LaravelProvider\RailtConfiguration;

/**
 * Class GraphQLController
 * @package Railt\LaravelProvider\Controllers
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

        if ($request->route()) {
            $this->action = $request->route()->getName();
        }
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
        $this->registerAutoloader($endpoint->getAutoloader());

        $this->getRouter($endpoint->getRouter());

        $response = $endpoint->request($this->getSchemaFile(), $request);

        return $response->toArray();
    }

    /**
     * @param Router $router
     */
    private function getRouter(Router $router): void
    {
        $path = $this->config->getRouterPathname($this->action);

        if (is_file($path)) {
            require $path;
        }
    }

    /**
     * @return View
     */
    public function graphiql(): View
    {
        $route = $this->config->getRouteForAction($this->action);

        return view('railt::graphiql', [
            'route' => $route
        ]);
    }

    /**
     * @return File
     */
    private function getSchemaFile(): File
    {
        return File::path($this->config->getSchemaPathname($this->action));
    }

    /**
     * @param Autoloader $autoloader
     */
    private function registerAutoloader(Autoloader $autoloader): void
    {
        foreach ($this->config->getAutoloadPaths($this->action) as $rule) {
            if (is_callable($rule)) {
                $autoloader->autoload($rule);
            } elseif (is_string($rule) || is_array($rule)) {
                $autoloader->dir($rule);
            }
        }
    }
}
