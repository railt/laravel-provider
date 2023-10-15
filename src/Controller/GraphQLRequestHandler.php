<?php

declare(strict_types=1);

namespace Railt\LaravelProvider\Controller;

use Railt\Contracts\Http\Factory\RequestFactoryInterface;
use Railt\Foundation\ConnectionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class GraphQLRequestHandler
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly RequestFactoryInterface $requests,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = $request->toArray();

        if (!isset($data['query'])) {
            throw new BadRequestHttpException('Missing required GraphQL query string');
        }

        if (!\is_string($data['query'])) {
            $message = \vsprintf('GraphQL query must be a string, but %s given', [
                \get_debug_type($data['query']),
            ]);

            throw new BadRequestHttpException($message);
        }

        if (isset($data['variables']) && !\is_array($data['variables'])) {
            $message = \vsprintf('GraphQL variables list must be an object, but %s given', [
                \get_debug_type($data['variables']),
            ]);

            throw new BadRequestHttpException($message);
        }

        if (isset($data['operationName']) && !\is_string($data['operationName'])) {
            $message = \vsprintf('GraphQL operation name must be a string, but %s given', [
                \get_debug_type($data['variables']),
            ]);

            throw new BadRequestHttpException($message);
        }

        $request = $this->requests->createRequest(
            query: $data['query'],
            variables: $data['variables'] ?? [],
            operationName: $data['operationName'] ?? null,
        );

        $response = $this->connection->handle($request);

        return new JsonResponse(
            data: $response->toArray(),
            status: $response->isSuccessful() ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
