<p align="center">
    <a href="https://railt.org"><img src="https://avatars.githubusercontent.com/u/31258828?s=300" width="150" alt="Railt" /></a>
</p>
<p align="center">
    <a href="https://packagist.org/packages/railt/laravel-provider"><img src="https://poser.pugx.org/railt/laravel-provider/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://railt.org"><img src="https://img.shields.io/badge/docs-site-6f4ca5.svg?style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAclBMVEUAAAD///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////9eWEHEAAAAJXRSTlMAoBzg8fxU9iFgsvjQwblyZdQYrYR0a1oT6dqlkH93TjQNC6N2001YMwAAAM5JREFUOMvNUtkOgzAMS3pwdMA4BjvZxfz/v7hOIEAt2hMP+MWN4kRRbdoYxMfIMJSmFsvtTmOA5gVJoLADkigqYB8qcPsNUOVQpd2kkFdA48wDe8rQkkWLjPbAfIedsv2T1uWvKLU+WYWa39HBEB2R9lWKI1EFphUhD5QpyqXLo4DTd4gns8ujIA6Dc3G/xC6PggjJ9ZYgcnl2BOIHpMcTGOK1Y4/XBPtfbcB/zapHs3y7D5PdfmBEHxgDNMuRK6bIeRDshtaX1EPsS9oWvv3QFx9Wvu0UAAAAAElFTkSuQmCC" alt="railt.org"></a>
    <a href="https://discord.gg/ND7SpD4"><img src="https://img.shields.io/badge/discord-chat-6f4ca5.svg?style=for-the-badge&logo=discord&logoColor=ffffff" alt="Discord"></a>
    <a href="https://packagist.org/packages/railt/laravel-provider"><img src="https://poser.pugx.org/railt/laravel-provider/v?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/laravel-provider"><img src="https://poser.pugx.org/railt/laravel-provider/v/unstable?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/railt/laravel-provider/master/LICENSE.md"><img src="https://poser.pugx.org/railt/laravel-provider/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/railt/laravel-provider/actions?workflow=Testing"><img src="https://github.com/railt/laravel-provider/workflows/tests/badge.svg" alt="Testing" /></a>
</p>

This Laravel Provider provides integration of [GraphQL](https://facebook.github.io/graphql/) 
using [Railt GraphQL](https://github.com/railt/railt).

## Requirements

- php: `^8.1`
- laravel: `^9.0|^10.0`
- railt/railt: `^2.0`

## Installation

Library is available as composer repository and can be installed using the 
following command in a root of your project.

```shell
$ composer require railt/laravel-provider
```

Then add provider into the `config/app.php` file [to the `providers` list](https://laravel.com/docs/10.x/providers#registering-providers):

```php
    'providers' => [
        // ...
        /*
         * Package Service Providers...
         */
         Railt\LaravelProvider\RailtServiceProvider::class,
         
         /*
          * Application Service Providers...
          */
         // ...
    ],
```

To publish an assets (config file, example files, etc), use the command:

```shell
$ php artisan vendor:publish --tag=railt
```

## Configuration

All application configs are located in the `config/railt.php` file.

Default routes of the main application:
- `/graphql` - For the GraphQL requests.
- `/graphiql` - For the GraphQL playground (GraphQL Web IDE).

### All Configuration Options

Below is an example of a provider configuration with all possible options.

```php
<?php

return [
    /*
    |---------------------------------------------------------------------------
    | List of Compilers
    |---------------------------------------------------------------------------
    |
    | ```
    | 'compilers' => [
    |   <name-1> => [
    |     'option' => 'value-1',
    |   ],
    |   <name-2> => [
    |     'option' => 'value-2',
    |   ],
    | ],
    | ```
    */
    'compilers' => [
        'default' => [
            /*
            | Reference to the cache store.
            |
            | See "cache.stores" in /config/cache.php.
            |
            | default: null
            */
            'cache' => null,

            /*
            | Compiler's specification version.
            |
            | Should be one of:
            | - "railt" - Modern extended version of specification.
            | - "draft" - See https://spec.graphql.org/draft/
            | - "october-2021" - See https://spec.graphql.org/October2021/
            | - "june-2018" - See https://spec.graphql.org/June2018/
            | - "october-2016" - See https://spec.graphql.org/October2016/
            | - "april-2016" - See https://spec.graphql.org/April2016/
            | - "october-2015" - See https://spec.graphql.org/October2015/
            | - "july-2015" - See https://spec.graphql.org/July2015/
            |
            | default: "railt"
            */
            'spec' => 'railt',

            /*
            | Reference to predefined types service.
            |
            | Should be instance of `Railt\TypeSystem\DictionaryInterface`.
            |
            | default: null
            */
            'types' => null,

            /*
            | Autogenerated root types stubs.
            */
            'generate' => [
                /*
                | Generated root object type name for queries.
                |
                | default: "Query"
                */
                'query' => 'Query',

                /*
                | Generated root object type name for mutations.
                |
                | default: null
                */
                'mutation' => null,

                /*
                | Generated root object type name for subscriptions.
                |
                | default: null
                */
                'subscription' => null,
            ],

            /*
            | Auto casting types compiler's options.
            */
            'cast' => [
                /*
                | Allow to cast integer values as floats.
                |
                | ```
                | input Example {
                |
                |   "Allow Int(1) as default of Float"
                |   inCaseOfEnabled(arg: Float = 1): Any
                |
                |   "Allow only Float(1.0) as default of Float"
                |   inCaseOfDisabled(arg: Float = 1.0): Any
                |
                | }
                | ```
                |
                | default: true
                */
                'int_to_float' => true,

                /*
                | Allow to cast scalar values as strings.
                |
                | ```
                | input Example {
                |
                |   "Allow Float(1.0) as default of String"
                |   inCaseOfEnabled(arg: String = 1.0): Any
                |
                |   "Allow only String("1.0") as default of String"
                |   inCaseOfDisabled(arg: String = "1.0"): Any
                |
                | }
                | ```
                |
                | default: true
                */
                'scalar_to_string' => true,
            ],

            /*
            | Default values extraction logic.
            */
            'extract' => [
                /*
                | Allow to extract nullable types as default values.
                |
                | ```
                | input Example {
                |
                |   "Allow nullables as default values"
                |   inCaseOfEnabled(arg: String): Any
                |
                |   "In case of disabled the default value must be defined explicitly"
                |   inCaseOfDisabled(arg: String = null): Any
                |
                | }
                | ```
                |
                | default: true
                */
                'nullable' => true,

                /*
                | Allow to extract list types as default values.
                |
                | ```
                | input Example {
                |
                |   "Allow lists as default values"
                |   inCaseOfEnabled(arg: [String]!): Any
                |
                |   "In case of disabled the default value must be defined explicitly"
                |   inCaseOfDisabled(arg: [String]! = []): Any
                |
                | }
                | ```
                |
                | default: true
                */
                'list' => true,
            ],

            /*
            | List of directories from which GraphQL files should be loaded.
            |
            | In the case that a "resource_path('graphql')" directory is
            | specified, then in case when assembling the schema, type "Example" is
            | required (for example: `field(arg: Example): String`) then
            | "/resources/graphql/Example.graphqls" or
            | "/resources/graphql/Example.graphql" will be loaded (if exists).
            |
            | default: []
            */
            'autoload' => [
                \resource_path('graphql'),
            ],
        ],
    ],

    /*
    |---------------------------------------------------------------------------
    | List of public GraphQL endpoints.
    |---------------------------------------------------------------------------
    |
    | ```
    | 'endpoints' => [
    |   <name-1> => [
    |     'option' => 'value-1',
    |   ],
    |   <name-2> => [
    |     'option' => 'value-2',
    |   ],
    | ],
    | ```
    */
    'endpoints' => [
        'default' => [
            /*
            | URI pathname to the GraphQL endpoint.
            |
            | required
            */
            'route' => '/graphql',

            /*
            | List or available route methods.
            |
            | default: ['post']
            */
            'methods' => ['get', 'post', 'put', 'patch'],

            /*
            | Pathname to the GraphQL schema file.
            |
            | required
            */
            'schema' => \resource_path('graphql/schema.graphqls'),

            /*
            | List of variables passed to the schema file.
            |
            | You can use these values inside the schema file:
            |
            | ```
            | variables:
            |   exampleController: "Path\To\ExampleController"
            | ```
            |
            | ```
            | type UserList {
            |     get(count: Int! = 100): [User!]
            |         @route(action: $exampleController)
            | }
            | ```
            |
            | default: []
            */
            'variables' => [
                'isDebug' => env('APP_DEBUG'),
            ],

            /*
            | Reference to defined compiler (from "compilers" section) name or
            | reference to Symfony's DI service.
            |
            | default: null
            */
            'compiler' => 'default',

            /*
            | List of Laravel middleware.
            |
            | default: []
            */
            'middleware' => [],

            /*
            | List of Railt GraphQL extensions (plugins).
            |
            | Should be instance of `Railt\Foundation\Extension\ExtensionInterface`.
            |
            | default: [ Railt\Extension\Router\RouterExtension ]
            */
            'extensions' => [
                Railt\Extension\Router\RouterExtension::class,
            ],
        ],
    ],

    /*
    |---------------------------------------------------------------------------
    | GraphQL Playground
    |---------------------------------------------------------------------------

    |
    | List of GraphQL playground (GraphiQL) endpoints.
    | See: https://github.com/graphql/graphiql
    |
    | ```
    | 'playground' => [
    |   <name-1> => [
    |     'option' => 'value-1',
    |   ],
    |   <name-2> => [
    |     'option' => 'value-2',
    |   ],
    | ],
    | ```
    */
    'playground' => [
        'default' => [
            /*
            | Reference to "endpoints" section for which this
            | playground will be used.
            |
            | required
            */
            'endpoint' => 'default',

            /*
            | URI pathname of playground.
            |
            | required
            */
            'route' => '/graphiql',

            /*
             | List or available route methods.
             |
             | default: ['get']
             */
            'methods' => ['get', 'head', 'options'],

            /*
            | List of route Laravel middleware.
            |
            | default: []
            */
            'middleware' => [],

            /*
            | List of additional optional headers that be used for each request.
            |
            | default: []
            */
            'headers' => [
                'X-Api-Playground' => 'GraphiQL',
            ],
        ],
    ],
];
```
