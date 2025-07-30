<?php

namespace App\Domain\Event\ValueObjects;

use App\Domain\Shared\ValueObjects\IBaseValueObject;

class EventStatus implements IBaseValueObject
{
    public function __construct(private string $status)
    {
        if (!in_array($status, ['upcoming', 'ongoing', 'completed'])) {
            throw new \InvalidArgumentException("Status 'upcoming', 'ongoing' yoki 'completed' qiymatida bo'lishi kerak!", 400);
        }
    }

    public function value(): string
    {
        return $this->status;
    }

    public function equals(IBaseValueObject $otherStatus): bool
    {
        return $this->status === $otherStatus;
    }

    public function __tostring(): string
    {
        return $this->status;
    }
}
