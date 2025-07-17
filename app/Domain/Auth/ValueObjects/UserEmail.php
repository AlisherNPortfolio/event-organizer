<?php

namespace App\Domain\Auth\ValueObjects;

use App\Domain\Shared\ValueObjects\IBaseValueObject;
use InvalidArgumentException;

final class UserEmail implements IBaseValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$value}");
        }
        $this->value = strtolower($value);
    }

    public static function from(string $email): self
    {
        return new self($email);
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
