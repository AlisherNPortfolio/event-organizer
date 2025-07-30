<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

final class EventTitle
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (empty($trimmed)) {
            throw new InvalidArgumentException('Event title bo\'sh bo\'lmasligi kerak');
        }
        if (strlen($trimmed) > 100) { // TODO: belgilar sonini configdan oladigan qilish.
            throw new InvalidArgumentException('Event title 100 ta belgidan oshmasligi kerak');
        }
        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
