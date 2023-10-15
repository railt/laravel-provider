<?php

declare(strict_types=1);

namespace Railt\LaravelProvider\Controller;

use Illuminate\Http\Response;

final class PlaygroundRequestHandler
{
    /**
     * @param non-empty-string $route
     * @param array<non-empty-string, non-empty-string> $headers
     */
    public function __construct(
        private readonly string $route,
        private readonly array $headers,
    ) {
    }

    public function __invoke(): Response
    {
        \ob_start();

        include \dirname(__DIR__, 2) . '/resources/playground/graphiql.html.php';

        return new Response(\ob_get_clean(), Response::HTTP_OK);
    }
}
