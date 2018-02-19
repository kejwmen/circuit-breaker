<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\State;

use kejwmen\CircuitBreaker\Circuit\Circuit;

interface CircuitStateHandler
{
    public function setCircuit(Circuit $circuit): void;
    public function isOpen(): bool;
    public function isHalfOpen(): bool;
    public function isClosed(): bool;
    public function reportSuccess();
    public function reportFailure();
}
