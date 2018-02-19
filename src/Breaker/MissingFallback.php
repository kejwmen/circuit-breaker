<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Breaker;

final class MissingFallback extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            \sprintf('Missing fallback')
        );
    }
}
