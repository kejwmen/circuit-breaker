<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit\State;

use kejwmen\CircuitBreaker\Circuit\Circuit;
use kejwmen\CircuitBreaker\Circuit\State\CircuitStateHandler;

final class UndefinedStateHandler implements CircuitStateHandler
{
    public function setCircuit(Circuit $circuit): void {}

    public function isOpen(): bool
    {
        return false;
    }

    public function isHalfOpen(): bool
    {
        return false;
    }

    public function isClosed(): bool
    {
        return false;
    }

    public function reportSuccess() {}

    public function reportFailure() {}
}
