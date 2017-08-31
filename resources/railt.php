<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

use Railt\Adapters\Laravel\Controllers\GraphQLController;

return [
    /**
     * Base framework configuration
     */
    'config' => [

        /**
         * The prefix for all route names
         */
        'routes_prefix' => 'railt.',
    ],

    /**
     * Laravel routes configuration.
     *
     * <code>
     *  'route_name' => [ options ]
     * </code>
     */
    'routes' => [
        'default' => [
            'url'  => '/graphql',
            'uses' => GraphQLController::class . '@index'
        ],
    ],

    /**
     * Configuration for application
     */
    'default' => [
        /**
         * Root GraphQL schema file path
         */
        'schema'   => resource_path('graphql/schema.graphqls'),

        /**
         * Autoload schema paths
         *
         *  PSR-0 Autoloading:
         *      'path/to/directory/User.gql' for User type.
         * <code>
         *  'path/to/directory'
         * </code>
         *
         *  Custom loader:
         * <code>
         *  function (string $type): ?string {
         *      return ... // File path if found or null otherwise
         *  }
         * </code>
         */
        'autoload' => [
            resource_path('graphql'),
        ],

        /**
         * Router file path
         */
        'router'   => base_path('routes/graphql.php'),
    ],
];
