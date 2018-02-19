<?php
declare(strict_types=1);

namespace kejwmen\CircuitBreaker\Circuit\Event;

final class CircuitEventType 
{
    public const BROKEN = 'broken';
    public const HALF_OPENED = 'half-opened';
    public const RESET = 'reset';

    /**
     * @var string
     */
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function broken(): self
    {
        return new self(self::BROKEN);
    }

    public static function halfOpened(): self
    {
        return new self(self::HALF_OPENED);
    }

    public static function reset(): self
    {
        return new self(self::RESET);
    }

    public function equals(self $otherType): bool
    {
        return $otherType->type === $this->type;
    }

    public function toString(): string
    {
        return $this->type;
    }
}
