<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Breaker;

interface Breaker
{
    public function execute(callable $function);
    public function executeWithFallback(callable $function, callable $fallback = null);
}
