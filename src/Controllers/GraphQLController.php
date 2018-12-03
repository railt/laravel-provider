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
use Railt\Http\Request;
use Railt\Http\ResponseInterface;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\LaravelProvider\Config;
use Railt\SDL\Schema\CompilerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GraphQLController
 */
class GraphQLController
{
    private const FILE_EXTENSIONS = [
        '.graphqls',
        '.graphql',
        '.gql'
    ];

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
        $this->app    = new Application($config->isDebug(), $app);
        $this->config = $config;
    }

    /**
     * @param array $directories
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Railt\Io\Exception\NotReadableException
     */
    private function bootAutoload(array $directories): void
    {
        /** @var CompilerInterface $compiler */
        $compiler = $this->app->get(CompilerInterface::class);
        $compiler->autoload(function (string $type) use ($directories): ?Readable {
            foreach (self::FILE_EXTENSIONS as $ext) {
                foreach ($directories as $dir) {
                    $pathName = $dir . '/' . $type . $ext;
                    if (\is_file($pathName)) {
                        return File::fromPathname($pathName);
                    }
                }
            }
            return null;
        });
    }

    /**
     * @param Request $request
     * @param HttpRequest $http
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \InvalidArgumentException
     * @throws \Railt\SDL\Exceptions\TypeNotFoundException
     * @throws \Railt\SDL\Exceptions\CompilerException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Throwable
     */
    public function handle(Request $request, HttpRequest $http): Response
    {
        try {
            $endpoint = $this->config->getEndpoint($http->route()->getName());
        } catch (\OutOfRangeException $e) {
            throw new NotFoundHttpException('Invalid GraphQL Schema');
        }

        foreach ($endpoint->getExtensions() as $extension) {
            $this->app->extend($extension);
        }

        $this->bootAutoload($endpoint->getAutoload());

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
