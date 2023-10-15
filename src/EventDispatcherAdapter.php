<?php

declare(strict_types=1);

namespace Railt\LaravelProvider;

use Illuminate\Contracts\Events\Dispatcher as LaravelEventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherAdapter implements EventDispatcherInterface
{
    public function __construct(
        private readonly LaravelEventDispatcher $dispatcher,
    ) {}

    public function dispatch(object $event): object
    {
        $this->dispatcher->dispatch($event);

        return $event;
    }
}
