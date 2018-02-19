<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit\State;

use kejwmen\CircuitBreaker\Circuit\Circuit;
use kejwmen\CircuitBreaker\Circuit\CircuitConfiguration;
use kejwmen\CircuitBreaker\Circuit\DefaultCircuit;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitBroken;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitHalfOpened;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitReset;
use kejwmen\CircuitBreaker\Circuit\FixedCircuitConfiguration;
use kejwmen\CircuitBreaker\Circuit\State\ConfigurableCircuitStateHandler;
use PHPUnit\Framework\TestCase;
use Test\kejwmen\CircuitBreaker\Circuit\Event\TestEventDispatcher;
use Test\kejwmen\CircuitBreaker\Circuit\TestCircuit;

final class ConfigurableCircuitStateHandlerTest extends TestCase
{
    private const DEFAULT_FAILURE_LIMIT = 1;
    private const DEFAULT_RESET_TIMEOUT = 50;

    /**
     * @var TestEventDispatcher
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = new TestEventDispatcher();
    }

    /**
     * @test
     */
    public function shouldReportSuccessForClosedCircuitAndDoNothing()
    {
        // given
        $handler = $this->getHandler();

        // when
        $handler->reportSuccess();

        // then
        $this->assertTrue($handler->isClosed());
        $this->assertCount(0, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function shouldReportSuccessForHalfOpenCircuitAndResetIt()
    {
        // given
        $circuit = new TestCircuit(
            'foo',
            self::DEFAULT_FAILURE_LIMIT + 1,
            self::DEFAULT_RESET_TIMEOUT + 1
        );

        $handler = $this->getHandler($circuit);

        // when
        $handler->reportSuccess();

        // then
        $this->assertTrue($handler->isClosed());
        $this->assertCount(1, $this->eventDispatcher);
        $this->assertTrue($this->eventDispatcher->hasOfTypeAndCircuit(CircuitReset::class, $circuit));
    }

    /**
     * @test
     */
    public function shouldReportFailureForCloseCircuitAndBreakIt()
    {
        // given
        $circuit = new TestCircuit('foo');

        $handler = $this->getHandler($circuit, new FixedCircuitConfiguration(0, 50));

        // when
        $handler->reportFailure();

        // then
        $this->assertTrue($handler->isOpen());
        $this->assertCount(1, $this->eventDispatcher);
        $this->assertTrue($this->eventDispatcher->hasOfTypeAndCircuit(CircuitBroken::class, $circuit));
    }

    /**
     * @test
     */
    public function shouldHalfOpenOpenCircuitAfterResetTimeout()
    {
        // given
        $circuit = new TestCircuit('foo', 2, 2);

        $handler = $this->getHandler($circuit, new FixedCircuitConfiguration(1, 1));

        // when
        $isHalfOpened = $handler->isHalfOpen();

        // then
        $this->assertTrue($isHalfOpened);
        $this->assertCount(1, $this->eventDispatcher);
        $this->assertTrue($this->eventDispatcher->hasOfTypeAndCircuit(CircuitHalfOpened::class, $circuit));
    }

    private function getHandler(Circuit $circuit = null, CircuitConfiguration $configuration = null): ConfigurableCircuitStateHandler
    {
        $handler = new ConfigurableCircuitStateHandler(
            $configuration ?? $this->defaultConfiguration(),
            $this->eventDispatcher
        );

        $handler->setCircuit($circuit ?? new DefaultCircuit('foo'));

        return $handler;
    }

    private function defaultConfiguration(): CircuitConfiguration
    {
        return new FixedCircuitConfiguration(
            self::DEFAULT_FAILURE_LIMIT,
            self::DEFAULT_RESET_TIMEOUT
        );
    }
}
