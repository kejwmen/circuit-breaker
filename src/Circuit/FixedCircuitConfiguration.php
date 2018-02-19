<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

final class FixedCircuitConfiguration implements CircuitConfiguration
{
    /**
     * @var int
     */
    private $failureLimit;

    /**
     * @var int
     */
    private $resetTimeout;

    public function __construct(int $failureLimit, int $resetTimeout)
    {
        $this->failureLimit = $failureLimit;
        $this->resetTimeout = $resetTimeout;
    }

    public function failureLimit(): int
    {
        return $this->failureLimit;
    }

    public function resetTimeout(): int
    {
        return $this->resetTimeout;
    }
}
