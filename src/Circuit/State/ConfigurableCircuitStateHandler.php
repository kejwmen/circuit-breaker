<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\State;

use kejwmen\CircuitBreaker\Circuit\Circuit;
use kejwmen\CircuitBreaker\Circuit\CircuitConfiguration;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitBroken;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitHalfOpened;
use kejwmen\CircuitBreaker\Circuit\Event\CircuitReset;
use kejwmen\CircuitBreaker\Circuit\Event\Dispatcher\EventDispatcher;

final class ConfigurableCircuitStateHandler implements CircuitStateHandler
{
    /**
     * @var Circuit
     */
    private $circuit;

    /**
     * @var CircuitConfiguration
     */
    private $configuration;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(CircuitConfiguration $configuration, EventDispatcher $eventDispatcher)
    {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setCircuit(Circuit $circuit): void
    {
        $this->circuit = $circuit;
    }

    public function reportSuccess()
    {
        if ($this->circuit->lastFailure()) {
            $this->circuit->reset();
            $this->eventDispatcher->dispatch(new CircuitReset($this->circuit));
        }
    }

    public function reportFailure()
    {
        $wasOpen = $this->isOpen();

        $this->circuit->countFailure();

        if (!$wasOpen && $this->isOpen()) {
            $this->eventDispatcher->dispatch(new CircuitBroken($this->circuit));
        }
    }

    public function isOpen(): bool
    {
        return ($this->circuit->failureCount() > $this->configuration->failureLimit())
            && !$this->resetTimeoutElapsed();
    }

    public function isHalfOpen(): bool
    {
        $predicate = ($this->circuit->failureCount() > $this->configuration->failureLimit())
            && $this->resetTimeoutElapsed();

        if ($predicate) {
            $this->eventDispatcher->dispatch(new CircuitHalfOpened($this->circuit));
        }

        return $predicate;
    }

    private function resetTimeoutElapsed(): bool
    {
        return ((time() - $this->circuit->lastFailure()) > $this->configuration->resetTimeout());
    }

    public function isClosed(): bool
    {
        return ($this->circuit->failureCount() <= $this->configuration->failureLimit());
    }
}
