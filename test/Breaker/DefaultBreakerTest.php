<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Breaker;

use kejwmen\CircuitBreaker\Breaker\Breaker;
use kejwmen\CircuitBreaker\Breaker\DefaultBreaker;
use kejwmen\CircuitBreaker\Breaker\MissingFallback;
use kejwmen\CircuitBreaker\Circuit\FixedCircuitConfiguration;
use kejwmen\CircuitBreaker\Circuit\InMemoryCircuitStore;
use kejwmen\CircuitBreaker\Circuit\State\CircuitStateHandler;
use kejwmen\CircuitBreaker\Circuit\State\ConfigurableCircuitStateHandler;
use PHPUnit\Framework\TestCase;
use Test\kejwmen\CircuitBreaker\Circuit\Event\TestEventDispatcher;
use Test\kejwmen\CircuitBreaker\Circuit\State\UndefinedStateHandler;
use Test\kejwmen\CircuitBreaker\Circuit\TestCircuit;

/**
 * @covers \kejwmen\CircuitBreaker\Breaker\DefaultBreaker
 */
final class DefaultBreakerTest extends TestCase
{
    private const DEFAULT_FAILURE_LIMIT = 1;
    private const DEFAULT_RESET_TIMEOUT = 50;

    /**
     * @var CircuitStateHandler
     */
    private $stateHandler;

    /**
     * @var InMemoryCircuitStore
     */
    private $store;

    /**
     * @var TestEventDispatcher
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = new TestEventDispatcher();
        $this->stateHandler = new ConfigurableCircuitStateHandler(
            new FixedCircuitConfiguration(self::DEFAULT_FAILURE_LIMIT, self::DEFAULT_RESET_TIMEOUT),
            $this->eventDispatcher
        );

        $this->store = new InMemoryCircuitStore();
    }

    /**
     * @test
     */
    public function shouldExecuteSimpleCallback()
    {
        // given
        $givenFunction = function () {
            return 42;
        };

        $breaker = $this->createBreaker();

        // when
        $result = $breaker->execute($givenFunction);

        // then
        $this->assertSame(42, $result);
    }

    /**
     * @test
     */
    public function shouldExecuteThrowningCallbackAndRethrowException()
    {
        // given
        $callback = function () {
            throw new \Exception("bar");
        };

        $breaker = $this->createBreaker();

        // when
        $this->executeThrowingCallback($breaker, $callback);

        $this->assertFalse($this->stateHandler->isOpen());
    }

    /**
     * @test
     */
    public function shouldExecuteThrowingCallbackAndOpenCircuitWhenFailureLimitExceeded()
    {
        // given
        $givenFunction = function () {
            throw new \Exception("bar");
        };

        $breaker = $this->createBreaker();

        // when
        for ($i=1;$i<=2;$i++) {
            $this->executeThrowingCallback($breaker, $givenFunction);
        }

        $this->assertTrue($this->stateHandler->isOpen());
    }

    /**
     * @test
     */
    public function shouldNotExecuteCallbackWhenCircuitIsOpen()
    {
        // given
        $givenFunction = function () {
            return 42;
        };

        $this->fixtureOpenCircuit();

        $breaker = $this->createBreaker();

        // when
        $result = $breaker->execute($givenFunction);

        $this->assertNotSame(42, $result);
    }

    /**
     * @test
     */
    public function shouldExecuteFallbackWhenCircuitIsOpen()
    {
        // given
        $callback = function () {
            return 42;
        };

        $fallback = function () {
            return 43;
        };

        $this->fixtureOpenCircuit();

        $breaker = $this->createBreaker();

        // when
        $result = $breaker->executeWithFallback($callback, $fallback);

        $this->assertSame(43, $result);
    }

    /**
     * @test
     */
    public function shouldExecuteDefaultFallbackWhenCircuitIsOpen()
    {
        // given
        $callback = function () {
            return 42;
        };

        $defaultFallback = function () {
            return 43;
        };

        $this->fixtureOpenCircuit();

        $breaker = $this->createBreaker($defaultFallback);

        // when
        $result = $breaker->executeWithFallback($callback);

        $this->assertSame(43, $result);
    }

    /**
     * @test
     * @covers \kejwmen\CircuitBreaker\Breaker\MissingFallback
     */
    public function shouldThrowExecutingDefaultFallbackWhenFallbackIsNotDefined()
    {
        // given
        $callback = function () {
            return 42;
        };

        $this->fixtureOpenCircuit();
        $breaker = $this->createBreaker();

        // expect
        $this->expectException(MissingFallback::class);

        // when
        $breaker->executeWithFallback($callback);
    }

    /**
     * @test
     */
    public function shouldExecuteCallbackToCloseHalfOpenedCircuit()
    {
        // given
        $callback = function () {
            return 42;
        };

        $this->fixtureHalfOpenedCircuit();
        $breaker = $this->createBreaker();

        // when
        $result = $breaker->execute($callback);

        $this->assertSame(42, $result);
        $this->assertTrue($this->stateHandler->isClosed());
    }

    /**
     * @test
     */
    public function shouldExecuteThrowingCallbackToOpenHalfOpenedCircuit()
    {
        // given
        $callback = function () {
            throw new \Exception('bar');
        };

        $this->fixtureHalfOpenedCircuit();
        $breaker = $this->createBreaker();

        // when
        $this->executeThrowingCallback($breaker, $callback);

        $this->assertTrue($this->stateHandler->isOpen());
    }

    /**
     * @test
     */
    public function shouldThrowWhenCircuitStateIsUndefined()
    {
        // given
        $givenFunction = function () {
            return 42;
        };

        $breaker = new DefaultBreaker(
            'foo',
            $this->store,
            new UndefinedStateHandler(),
            null
        );

        // expect
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Undefined circuit state');

        // when
        $breaker->execute($givenFunction);
    }

    private function createBreaker(callable $defaultFallback = null): Breaker
    {
        return new DefaultBreaker(
            'foo',
            $this->store,
            $this->stateHandler,
            $defaultFallback
        );
    }

    private function fixtureOpenCircuit(): void
    {
        $this->store->store(new TestCircuit(
            'foo',
            self::DEFAULT_FAILURE_LIMIT + 1
        ));
    }

    private function fixtureHalfOpenedCircuit(): void
    {
        $this->store->store(new TestCircuit(
            'foo',
            self::DEFAULT_FAILURE_LIMIT + 1,
            self::DEFAULT_RESET_TIMEOUT + 1
        ));
    }

    private function executeThrowingCallback(Breaker $breaker, callable $callback): void
    {
        try {
            $breaker->execute($callback);
            $this->fail("Should throw");
        } catch (\Exception $exception) {
            $this->assertSame("bar", $exception->getMessage());
        }
    }
}
