<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Breaker;

use kejwmen\CircuitBreaker\Circuit\Circuit;
use kejwmen\CircuitBreaker\Circuit\CircuitStore;
use kejwmen\CircuitBreaker\Circuit\DefaultCircuit;
use kejwmen\CircuitBreaker\Circuit\State\CircuitStateHandler;

final class DefaultBreaker implements Breaker
{
    /**
     * @var callable|null
     */
    private $defaultFallback;

    /**
     * @var CircuitStore
     */
    private $store;

    /**
     * @var CircuitStateHandler
     */
    private $stateHandler;

    /**
     * @var Circuit
     */
    private $circuit;

    public function __construct(
        string $circuitName,
        CircuitStore $store,
        CircuitStateHandler $stateHandler,
        ?callable $defaultFallback
    ) {
        $this->store = $store;

        $this->circuit = $this->store->fetch($circuitName) ?? new DefaultCircuit($circuitName);

        $stateHandler->setCircuit($this->circuit);
        $this->stateHandler = $stateHandler;
        $this->defaultFallback = $defaultFallback;
    }

    public function execute(callable $function)
    {
        return $this->call($function, null);
    }

    public function executeWithFallback(callable $function, callable $fallback = null)
    {
        if (!\is_callable($fallback) && !\is_callable($this->defaultFallback)) {
            throw new MissingFallback();
        }

        return $this->call($function, $fallback ?? $this->defaultFallback);
    }

    private function onFailure(): void
    {
        $this->stateHandler->reportFailure();
        $this->store->store($this->circuit);
    }

    private function onSuccess(): void
    {
        $this->stateHandler->reportSuccess();
        $this->store->store($this->circuit);
    }

    private function call(callable $function, ?callable $fallback)
    {
        if ($this->stateHandler->isHalfOpen()) {
            try {
                $result = $function();
                $this->onSuccess();

                return $result;
            } catch (\Exception $exception) {
                $this->onFailure();
                throw $exception;
            }
        } elseif ($this->stateHandler->isOpen()) {
            $fallback = $fallback ?? $this->defaultFallback;
            if (\is_callable($fallback)) {
                return $fallback();
            }

            return;
        } elseif ($this->stateHandler->isClosed()) {
            try {
                $result = $function();
                $this->onSuccess();

                return $result;
            } catch (\Exception $exception) {
                $this->onFailure();
                throw $exception;
            }
        } else {
            throw new \Exception("Undefined circuit state");
        }
    }
}
