<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event\Dispatcher;

use kejwmen\CircuitBreaker\Circuit\Event\CircuitEvent;

interface EventDispatcher
{
    public function dispatch(CircuitEvent $event): void;
}
