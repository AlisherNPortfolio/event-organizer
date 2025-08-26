<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventTitle;
use Illuminate\Http\UploadedFile;

class EditEventCommand extends Command
{
    public function __construct(
        public EventId $eventId,
        public EventTitle $title,
        public EventDescription $description,
        public string $address,
        public string $startTime,
        public int $minParticipants,
        public ?int $maxParticipants,
        public ?float $price,
        public ?string $currency,
        public ?string $endTime,
        public ?UploadedFile $image
    )
    {}
}
