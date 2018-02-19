<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event\Dispatcher;

use kejwmen\CircuitBreaker\Circuit\Event\CircuitEvent;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitEventType;

final class CallbackEventDispatcher implements EventDispatcher
{
    private $callbacks = [];

    public function __construct(
        ?callable $onBroken,
        ?callable $onHalfOpened,
        ?callable $onReset
    ) {
        $this->callbacks = [
            CircuitEventType::BROKEN => $onBroken,
            CircuitEventType::HALF_OPENED => $onHalfOpened,
            CircuitEventType::RESET => $onReset
        ];
    }

    public function dispatch(CircuitEvent $event): void
    {
        $callback = $this->callbacks[$event->type()->toString()] ?? function () {};

        $callback($event);
    }
}
