<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

interface CircuitStore
{
    public function store(Circuit $circuit);
    public function fetch(string $name): ?Circuit;
}
