<?php

namespace App\Domain\Auth\ValueObjects;

use App\Domain\Shared\ValueObjects\IBaseValueObject;
use InvalidArgumentException;

class UserId implements IBaseValueObject
{
    private string $id;

    public function __construct(string $id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('ID cannot be empty');
        }

        $this->id = $id;
    }

    public function value(): string
    {
        return $this->id;
    }

    public function equals(IBaseValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function generate(): self
    {
        return new self((string) \Illuminate\Support\Str::uuid());
    }
}
