<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Controllers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request as HttpRequest;
use Railt\Foundation\Application;
use Railt\LaravelProvider\Config;
use Railt\LaravelProvider\Request as GraphQLRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GraphQLController
 */
class GraphQLController
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * GraphQLController constructor.
     * @param Container $app
     * @param Config $config
     */
    public function __construct(Container $app, Config $config)
    {
        $this->app    = new Application($app, $config->isDebug());
        $this->config = $config;
    }

    /**
     * @param GraphQLRequest $request
     * @param HttpRequest $http
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Railt\SDL\Exceptions\TypeNotFoundException
     * @throws \Railt\SDL\Exceptions\CompilerException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function handle(GraphQLRequest $request, HttpRequest $http): array
    {
        try {
            $endpoint = $this->config->getEndpoint($http->route()->getName());
        } catch (\OutOfRangeException $e) {
            throw new NotFoundHttpException('Invalid GraphQL Schema');
        }

        foreach ($endpoint->getExtensions() as $extension) {
            $this->app->extend($extension);
        }

        $response = $this->app->request($endpoint->getSchema(), $request);

        return $response->toArray();
    }
}
