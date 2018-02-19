<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit;

use kejwmen\CircuitBreaker\Circuit\InMemoryCircuitStore;
use PHPUnit\Framework\TestCase;

final class InMemoryCircuitStoreTest extends TestCase
{
    /**
     * @test
     */
    public function shouldStoreAndReturnTheSameCircuitWhenFetched()
    {
        // given
        $circuit = new TestCircuit();
        $store = new InMemoryCircuitStore();

        // when
        $store->store($circuit);
        $fetchedCircuit = $store->fetch($circuit->name());

        // then
        $this->assertSame($circuit, $fetchedCircuit);
    }

    /**
     * @test
     */
    public function shouldReturnNullForNotStoredName()
    {
        // given
        $circuit = new TestCircuit();
        $store = new InMemoryCircuitStore();
        $store->store($circuit);

        // when
        $fetchedCircuit = $store->fetch($circuit->name() . 'foo');

        // then
        $this->assertNull($fetchedCircuit);
    }
}
