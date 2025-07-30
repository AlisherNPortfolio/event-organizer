<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

final class ParticipantLimit
{
    private int $min;
    private int $max;

    public function __construct(int $min, int $max)
    {
        if ($min < 1) { // TODO: config fayldan olish kerak.
            throw new InvalidArgumentException('Kamida 1 ta qatnashchi bo\'lishi kerak');
        }
        if ($max < $min) {
            throw new InvalidArgumentException('Maksimum qatnashchilar soni minimum miqdordan katta bo\'lishi kerak');
        }
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function canAccommodate(int $participantCount): bool
    {
        return $participantCount >= $this->min && $participantCount <= $this->max;
    }
}
