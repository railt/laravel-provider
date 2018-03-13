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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Railt\Foundation\Application;
use Railt\Http\ResponseInterface;
use Railt\LaravelProvider\Config;
use Railt\LaravelProvider\Request as GraphQLRequest;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \Railt\SDL\Exceptions\TypeNotFoundException
     * @throws \Railt\SDL\Exceptions\CompilerException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Throwable
     */
    public function handle(GraphQLRequest $request, HttpRequest $http): Response
    {
        try {
            $endpoint = $this->config->getEndpoint($http->route()->getName());
        } catch (\OutOfRangeException $e) {
            throw new NotFoundHttpException('Invalid GraphQL Schema');
        }

        foreach ($endpoint->getExtensions() as $extension) {
            $this->app->extend($extension);
        }

        return $this->toResponse($this->app->request($endpoint->getSchema(), $request), $http);
    }

    /**
     * @param ResponseInterface $response
     * @param HttpRequest $http
     * @return Response
     * @throws \Throwable
     */
    private function toResponse(ResponseInterface $response, HttpRequest $http): Response
    {
        if ($this->isError($response) && ! $this->wantsJson($http)) {
            throw \array_first($response->getExceptions());
        }

        return $this->toJsonResponse($response);
    }

    /**
     * @param HttpRequest $request
     * @return bool
     */
    private function wantsJson(HttpRequest $request): bool
    {
        return $request->isJson() || $request->wantsJson() || $request->isXmlHttpRequest();
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    private function isError(ResponseInterface $response): bool
    {
        return ! $response->isSuccessful();
    }

    /**
     * @param ResponseInterface $response
     * @return JsonResponse
     */
    private function toJsonResponse(ResponseInterface $response): JsonResponse
    {
        $json = new JsonResponse($response->toArray(), $response->getStatusCode());

        if ($this->config->isDebug()) {
            $options = $json->getEncodingOptions() | \JSON_PRETTY_PRINT;

            $json->setEncodingOptions($options);
        }

        return $json;
    }
}
