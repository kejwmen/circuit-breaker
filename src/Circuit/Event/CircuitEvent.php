<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event;

use kejwmen\CircuitBreaker\Circuit\Circuit;

interface CircuitEvent
{
    public function circuit(): Circuit;
    public function type(): CircuitEventType;
}
