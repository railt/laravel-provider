# Railt Http Component

<p align="center">
    <a href="https://travis-ci.org/railt/http"><img src="https://travis-ci.org/railt/http.svg?branch=master" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/http/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/http/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/http/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/http/badges/coverage.png?b=master" alt="Code coverage" /></a>
    <a href="https://packagist.org/packages/railt/http"><img src="https://poser.pugx.org/railt/http/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/http"><img src="https://poser.pugx.org/railt/http/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/railt/http/master/LICENSE"><img src="https://poser.pugx.org/railt/http/license" alt="License MIT"></a>
</p>

## About

The Http Component defines an object-oriented layer for the GraphQL specification based on HTTP protocol.

## Installation

> Make sure that you are using at least PHP 7.1

- `composer require railt/http`

## Creating the GraphQL Request

### Native

Create a HTTP request from php globals.

```php
use Railt\Http\Request;

/** @var \Railt\Http\RequestInterface $request */
$request = Request::create();
```

### Laravel

Create a HTTP request from php globals.

```php
use Railt\Http\Request;

/** 
 * @var \Illuminate\Http\Request $laravelRequest
 * @var \Railt\Http\RequestInterface $request
 */
$request = Request::create($laravelRequest);
```

### Symfony

Create a HTTP request from php globals.

```php
use Railt\Http\Request;

/** 
 * @var \Symfony\Component\HttpFoundation\Request $symfonyRequest
 * @var \Railt\Http\RequestInterface $request
 */
$request = Request::create($symfonyRequest);
```

## Request API

```php
/**
 * @var string $query The GraphQL query string
 */
$query = $request->getQuery();

/**
 * @var array $variables GraphQL query variables (hash-map / assoc. array)
 */
$variables = $request->getVariables();

/**
 * @var string|null $operation The GraphQL query type. Can contain "query", "mutation" or "subscribtion" string.
 */
$operation = $request->getOperation();
```

## Customization

By default request object selects a POST or GET (by priority) arguments 
named "query", "variables" and "operation".
You can change this names using special setter methods.

```php
// HTTP /graphql?my_query_arguement={%20users%20{%20id,%20login%20}%20}

$query = $request->getQuery(); // string(0) ""

$request->setQueryArgument('my_query_arguement');

$query = $request->getQuery(); // string(25) "{ users { id, login } }"
```
