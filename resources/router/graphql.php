<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @var \Railt\Routing\Router $router
 */
declare(strict_types=1);

/**
 * Controllers
 */
$router->on('say', 'App\\GraphQL\\Controllers\\EchoController@say');


/**
 * Decorators
 */
$router->on('*', 'App\\GraphQL\\Decorators\\UpperCaseDecorator@upper');
