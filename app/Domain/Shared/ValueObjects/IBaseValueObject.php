<?php

namespace App\Domain\Shared\ValueObjects;

interface IBaseValueObject
{
    public function value(): string;
    public function equals(IBaseValueObject $other): bool;
    public function __toString(): string;
}
