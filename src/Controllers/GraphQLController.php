<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Railt\Foundation\ApplicationInterface;
use Railt\Http\Provider\ProviderInterface;
use Railt\Http\ResponseInterface;
use Railt\Io\File;

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
     * @var array
     */
    private $config;

    /**
     * GraphQLController constructor.
     * @param ApplicationInterface $app
     */
    public function __construct(ApplicationInterface $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config->get('railt');
    }

    /**
     * @param ProviderInterface $provider
     * @throws \Railt\Io\Exception\NotReadableException
     */
    public function handle(ProviderInterface $provider)
    {
        $schema = File::fromPathname($this->config['schema']);

        /** @var ResponseInterface $response */
        $response = $this->app->connect($schema)->requests($provider);

        $code = $response->hasErrors() ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK;

        return new JsonResponse($response->toArray(), $code, [], $response->isDebug() ? \JSON_PRETTY_PRINT : 0);
    }
}
