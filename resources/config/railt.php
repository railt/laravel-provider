<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

use Railt\LaravelProvider\Controllers\GraphQLController;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */
    'debug'     => env('RAILT_DEBUG', env('APP_DEBUG', false)),

    /**
     * Route path
     */
    'uri' => 'graphql',

    /**
     * Root GraphQL schema file path.
     */
    'schema'     => resource_path('graphql/schema.graphqls'),

    /**
     * GraphQL controller.
     */
    'uses'       => GraphQLController::class . '@handle',

    /**
     * Routes prefix.
     */
    'prefix'     => 'railt.',

    /**
     * GraphQL middleware.
     */
    'middleware' => ['api'],

    /**
     * Allowed methods.
     */
    'methods'    => ['GET', 'POST', 'PUT', 'PATCH'],
];
