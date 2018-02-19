<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit;

final class InMemoryCircuitStore implements CircuitStore
{
    /**
     * @var array
     */
    private $store = [];

    public function store(Circuit $circuit)
    {
        $this->store[$circuit->name()] = $circuit;
    }

    public function fetch(string $name): ?Circuit
    {
        return $this->store[$name] ?? null;
    }
}
