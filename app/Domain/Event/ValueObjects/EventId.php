<?php

namespace App\Domain\Event\ValueObjects;

use App\Domain\Shared\ValueObjects\Id;

final class EventId extends Id
{
    public static function generate(): self
    {
        return new self((string) \Illuminate\Support\Str::uuid());
    }
}
