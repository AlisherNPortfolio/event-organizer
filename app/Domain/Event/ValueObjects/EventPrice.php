<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

final class EventPrice
{
    private float $amount;
    private string $currency;
    private bool $isFree;

    public function __construct(float $amount = 0.0, string $currency = 'UZS') // TODO: valyuta turi configdan olinishi kerak
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Narx noldan kichik bo\'lishi mumkin emas');
        }
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->isFree = $amount == 0;
    }

    public static function free(): self
    {
        return new self(0.0);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function __toString(): string
    {
        if ($this->isFree) {
            return 'Bepul';
        }
        return number_format($this->amount, 0) . ' ' . $this->currency;
    }
}
