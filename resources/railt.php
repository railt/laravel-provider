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
    'config' => [
        'routes_prefix' => 'railt.'
    ],

    'routes' => [
        'default' => [
            'url' => '/graphql',
            'uses' => GraphQLController::class . '@index'
        ]
    ],

    'default' => [
        'schema' => resource_path('graphql/schema.graphqls'),
        'router' => base_path('routes/graphql.php'),
    ]
];
