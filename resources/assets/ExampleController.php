<?php

declare(strict_types=1);

namespace App\Http\Controllers\GraphQL;

final class ExampleController
{
    public function say(string $message): string
    {
        return \sprintf('Response %s', $message);
    }
}
