<?php

namespace App\Domain\Auth\ValueObjects;

use App\Domain\Shared\ValueObjects\IBaseValueObject;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

final class Password implements IBaseValueObject
{
    private string $hashedValue;

    public function __construct(string $plainPassword)
    {
        if (strlen($plainPassword) < 8) { // TODO: password length should be in config file
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }

        if (empty(trim($plainPassword))) {
            throw new InvalidArgumentException('Password cannot be empty');
        }

        $this->hashedValue = Hash::make($plainPassword);
    }

    public static function from(string $hashedPassword): self
    {
        $instance = new self(plainPassword: 'temporary');
        $instance->hashedValue = $hashedPassword;
        return $instance;
    }

    public function verify(string $plainPassword): bool
    {
        if (empty($plainPassword)) {
            return false;
        }

        return password_verify($plainPassword, $this->hashedValue);
    }

    public function value(): string
    {
        return $this->hashedValue;
    }

    public function equals(IBaseValueObject $other): bool
    {
        return $this->hashedValue === $other->value();
    }

    public function __toString(): string
    {
        return '[HIDDEN]';
    }
}
