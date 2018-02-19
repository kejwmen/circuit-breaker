<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit;

use kejwmen\CircuitBreaker\Circuit\DefaultCircuit;
use PHPUnit\Framework\TestCase;

final class DefaultCircuitTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnGivenName()
    {
        // given
        $circuit = new DefaultCircuit('foo');

        // when
        $name = $circuit->name();

        // then
        $this->assertSame('foo', $name);
    }

    /**
     * @test
     */
    public function shouldHaveNoFailures()
    {
        // when
        $circuit = new DefaultCircuit('foo');

        // then
        $lastFailure = $circuit->lastFailure();
        $failureCount = $circuit->failureCount();

        $this->assertNull($lastFailure);
        $this->assertSame(0, $failureCount);
    }

    /**
     * @test
     */
    public function shouldCountFailure()
    {
        // given
        $circuit = new DefaultCircuit('foo');

        // when
        $circuit->countFailure();

        // then
        $lastFailure = $circuit->lastFailure();
        $failureCount = $circuit->failureCount();

        $this->assertNotNull($lastFailure);
        $this->assertSame(1, $failureCount);
    }

    /**
     * @test
     */
    public function shouldResetToInitialState()
    {
        // given
        $circuit = new DefaultCircuit('foo');
        $circuit->countFailure();

        // when
        $circuit->reset();

        // then
        $lastFailure = $circuit->lastFailure();
        $failureCount = $circuit->failureCount();

        $this->assertNull($lastFailure);
        $this->assertSame(0, $failureCount);
    }
}
