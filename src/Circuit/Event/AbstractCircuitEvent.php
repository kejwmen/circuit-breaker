<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event;

use kejwmen\CircuitBreaker\Circuit\Circuit;

abstract class AbstractCircuitEvent implements CircuitEvent
{
    /**
     * @var Circuit
     */
    private $circuit;

    /**
     * @var CircuitEventType
     */
    private $type;

    public function __construct(Circuit $circuit, CircuitEventType $type)
    {
        $this->circuit = $circuit;
        $this->type = $type;
    }

    public function circuit(): Circuit
    {
        return $this->circuit;
    }

    public function type(): CircuitEventType
    {
        return $this->type;
    }
}
