<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

interface CircuitConfiguration
{
    public function failureLimit(): int;
    public function resetTimeout(): int;
}
