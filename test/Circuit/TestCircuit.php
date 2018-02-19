<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit;

use kejwmen\CircuitBreaker\Circuit\Circuit;

final class TestCircuit implements Circuit
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $lastFailure;

    /**
     * @var int
     */
    private $failureCount;

    public function __construct(string $name = 'foo', int $failuresBefore = 0, int $lastFailureAgo = 0)
    {
        $this->name = $name;
        $this->failureCount = $failuresBefore;

        if ($failuresBefore > 0) {
            $this->lastFailure = (time() - $lastFailureAgo);
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function failureCount(): int
    {
        return $this->failureCount;
    }

    public function lastFailure(): ?int
    {
        return $this->lastFailure;
    }

    public function countFailure(): void
    {
        $this->failureCount += 1;
        $this->lastFailure = \time();
    }

    public function reset(): void
    {
        $this->failureCount = 0;
        $this->lastFailure = null;
    }
}
