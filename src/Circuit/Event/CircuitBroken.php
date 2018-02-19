<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event;

use kejwmen\CircuitBreaker\Circuit\Circuit;

final class CircuitBroken extends AbstractCircuitEvent
{
    public function __construct(Circuit $circuit)
    {
        parent::__construct($circuit, CircuitEventType::broken());
    }
}
