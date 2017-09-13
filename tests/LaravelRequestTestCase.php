<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Tests;

use Railt\Http\RequestInterface;
use Railt\Adapters\Laravel\Request;
use Illuminate\Http\Request as LaravelNativeRequest;

/**
 * Class LaravelRequestTestCase
 * @package Railt\LaravelProvider\Tests
 */
class LaravelRequestTestCase extends AbstractHttpRequestTestCase
{
    /**
     * @param string $body
     * @param bool $makeJson
     * @return RequestInterface
     * @throws \LogicException
     */
    protected function request(string $body, bool $makeJson = true): RequestInterface
    {
        $request = LaravelNativeRequest::createFromGlobals();

        if ($makeJson) {
            $request->headers->set('Content-Type', 'application/json');
        }

        (function () use ($body) {
            $this->content = $body;
        })->call($request);

        return new Request($request);
    }
}
