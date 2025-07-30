<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

final class EventDescription
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (empty($trimmed)) {
            throw new InvalidArgumentException('Event description bo\'sh bo\'lmasligi kerak');
        }
        if (strlen($trimmed) > 2000) { // TODO: belgilar sonini configdan oladigan qilish
            throw new InvalidArgumentException('Event description 2000 ta belgidan oshmasligi kerak');
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
