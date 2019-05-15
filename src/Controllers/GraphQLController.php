<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Railt\Http\Factory;
use Railt\LaravelProvider\Config;
use Railt\Foundation\ApplicationInterface;
use Railt\LaravelProvider\Config\Endpoint;
use Railt\Http\ResponseInterface;
use Railt\LaravelProvider\Http\LaravelProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GraphQLController
 */
class GraphQLController
{
    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * GraphQLController constructor.
     *
     * @param ApplicationInterface $app
     * @param Config $config
     */
    public function __construct(ApplicationInterface $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function graphqlAction(Request $request): JsonResponse
    {
        $response = $this->execute($request);

        return new JsonResponse($response->toArray(), $response->getStatusCode(), [], $response->getJsonOptions());
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     * @throws NotFoundHttpException
     */
    private function execute(Request $request): ResponseInterface
    {
        $endpoint = $this->getEndpointByRoute($request);

        $connection = $this->app->connect($endpoint->getSchema());
        $factory = Factory::create(new LaravelProvider($request));

        return $connection->request($factory);
    }

    /**
     * @param Request $request
     * @return Endpoint
     * @throws NotFoundHttpException
     */
    private function getEndpointByRoute(Request $request): Endpoint
    {
        $route = $request->route()->getName();
        $endpoint = $this->config->findByRouteName($route);

        if ($endpoint === null) {
            $error = \sprintf('GraphQL endpoint for route "%s" was not registered', $route);
            throw new NotFoundHttpException($error);
        }

        return $endpoint;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     * @throws BindingResolutionException
     */
    public function playgroundAction(Request $request)
    {
        return \view('railt::playground', [
            'endpoints' => $this->config->getEndpoints(),
            'route'     => $request->route(),
            'ui'        => $this->config->getPlayground(),
            'debug'     => $this->config->isDebug(),
        ]);
    }
}
