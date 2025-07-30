<?php

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

abstract class Id implements IBaseValueObject
{
    protected string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('ID cannot be empty');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(IBaseValueObject $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
