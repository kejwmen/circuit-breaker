<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

final class DefaultCircuit implements Circuit
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var integer|null
     */
    private $lastFailure;

    /**
     * @var int
     */
    private $failureCount = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function lastFailure(): ?int
    {
        return $this->lastFailure;
    }

    public function failureCount(): int
    {
        return $this->failureCount;
    }

    public function countFailure(): void
    {
        $this->failureCount += 1;
        $this->lastFailure = time();
    }

    public function reset(): void
    {
        $this->failureCount = 0;
        $this->lastFailure = null;
    }
}
