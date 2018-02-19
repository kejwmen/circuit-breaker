<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

interface Circuit
{
    public function name(): string;
    public function failureCount(): int;
    public function lastFailure(): ?int;
    public function countFailure(): void;
    public function reset(): void;
}
