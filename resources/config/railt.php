<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

return [

    /*
     |--------------------------------------------------------------------------
     | Railt Debug Mode
     |--------------------------------------------------------------------------
     |
     | When your application is in debug mode, detailed error messages with
     | stack traces will be shown on every error that occurs within your
     | application. If disabled, a simple generic response will shown.
     |
     */

    'debug' => env('RAILT_DEBUG', env('APP_DEBUG', false)),

    /*
     |--------------------------------------------------------------------------
     | GraphQL API Endpoints
     |--------------------------------------------------------------------------
     |
     | List of access points to the GraphQL API.
     |
     */

    'endpoints'  => [
        'default' => [
            'route'      => 'graphql',
            'methods'    => ['GET', 'POST'],
            'schema'     => resource_path('graphql/schema.graphqls'),
            'middleware' => ['api'],
        ],

        // 'admin' => [
        //     'route'      => 'graphql/admin',
        //     'schema'     => resource_path('graphql/admin.graphqls'),
        //     'middleware' => ['api'],
        //     'name'       => 'admin.railt',
        //     'methods'    => ['GET', 'POST', 'PUT', 'PATCH'],
        // ]
    ],

    /*
     |--------------------------------------------------------------------------
     | Playground UI Settings
     |--------------------------------------------------------------------------
     |
     | - "enabled" - Playground is enabled by default, when debug is set to true
     |      in railt.php. You can override the value by setting enable to true
     |      or false instead of null.
     |
     | - "route" - Sometimes you want to set route to be used by playground to
     |      load its resources from.
     |
     | - "middleware" - Any middleware for the playground route.
     |
     */
    'playground' => [
        'enabled'    => env('RAILT_PLAYGROUND_ENABLED', null),
        'route'      => 'graphql/playground',
        'middleware' => ['web'],
        'settings'   => [
            'editor.cursorShape'          => 'line',
            'editor.fontFamily'           => "'Source Code Pro', 'Consolas', 'Inconsolata', 'Droid Sans Mono', 'Monaco', monospace",
            'editor.fontSize'             => 16,
            'editor.reuseHeaders'         => true,
            'editor.theme'                => 'light',
            'general.betaUpdates'         => false,
            'prettier.printWidth'         => 80,
            'prettier.tabWidth'           => 2,
            'prettier.useTabs'            => true,
            'request.credentials'         => 'omit',
            'schema.disableComments'      => false,
            'schema.enablePolling'        => false,
            'tracing.hideTracingResponse' => false,
        ]
    ],
];
