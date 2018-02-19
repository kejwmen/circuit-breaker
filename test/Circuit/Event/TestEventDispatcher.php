<?php
declare(strict_types=1);

namespace Test\kejwmen\CircuitBreaker\Circuit\Event;

use function Functional\select;
use function Functional\some;
use kejwmen\CircuitBreaker\Circuit\Circuit;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitEvent;
use kejwmen\CircuitBreaker\Circuit\Event\Dispatcher\EventDispatcher;

final class TestEventDispatcher implements EventDispatcher, \Countable
{
    /**
     * @var array
     */
    private $events = [];

    public function dispatch(CircuitEvent $event): void
    {
        $this->events[] = $event;
    }

    public function all(): array
    {
        return $this->events;
    }

    public function ofType(string $className): array
    {
        return select($this->events, function (CircuitEvent $event) use ($className) {
            return $event instanceof $className;
        });
    }

    public function hasOfType(string $className): bool
    {
        return some($this->events, function (CircuitEvent $event) use ($className) {
            return $event instanceof $className;
        });
    }

    public function hasOfTypeAndCircuit(string $className, Circuit $circuit): bool
    {
        return some($this->events, function (CircuitEvent $event) use ($className, $circuit) {
            return $event instanceof $className
                && $event->circuit() === $circuit;
        });
    }

    public function has(callable $predicate): bool
    {
        return some($this->events, $predicate);
    }

    public function count()
    {
        return \count($this->events);
    }
}
