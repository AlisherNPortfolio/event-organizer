<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\EventTitle;

class CreateEventCommand extends Command
{
    public function __construct(
        public readonly UserId $organizerId,
        public readonly EventTitle $title,
        public readonly EventDescription $description,
        public readonly string $address,
        public readonly int $minParticipants,
        public readonly int $maxParticipants,
        public readonly float $price,
        public readonly string $currency,
        public readonly array $images,
        public readonly string $startTime,
        public readonly ?string $endTime
    )
    {}
}
