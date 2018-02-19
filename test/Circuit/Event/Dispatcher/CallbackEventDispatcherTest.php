<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit\Event\Dispatcher;

use kejwmen\CircuitBreaker\Circuit\Event\CircuitBroken;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitEvent;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitEventType;
use kejwmen\CircuitBreaker\Circuit\Event\Dispatcher\CallbackEventDispatcher;
use PHPUnit\Framework\TestCase;
use Test\kejwmen\CircuitBreaker\Circuit\TestCircuit;

final class CallbackEventDispatcherTest extends TestCase
{
    private static $executedCallbackTypes = [];

    public function tearDown()
    {
        self::$executedCallbackTypes = [];
    }

    /**
     * @test
     * @dataProvider callbacksAndEventsProvider
     */
    public function shouldExecuteProperCallbackWhenGivenEventIsDispatched(
        ?callable $onBroken,
        ?callable $onHalfOpened,
        ?callable $onReset,
        array $events
    ) {
        // given
        $dispatcher = new CallbackEventDispatcher($onBroken, $onHalfOpened, $onReset);

        // when
        /** @var CircuitEvent $event */
        foreach ($events as $event) {
            $dispatcher->dispatch($event);
        }

        // then
        while ($event = \array_pop($events)) {
            $callbackType = \array_pop(self::$executedCallbackTypes);
            $this->assertTrue($event->type()->equals($callbackType));
        }
    }

    public function callbacksAndEventsProvider(): iterable
    {
        yield [
            $this->callbackForEventType(CircuitEventType::broken()),
            null,
            null,
            [
                new CircuitBroken(new TestCircuit())
            ]
        ];
    }

    private function callbackForEventType(CircuitEventType $type): callable
    {
        return function() use ($type) {
            self::$executedCallbackTypes[] = $type;
        };
    }
}
