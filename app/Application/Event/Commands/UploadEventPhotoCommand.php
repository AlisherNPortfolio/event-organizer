<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;

class UploadEventPhotoCommand extends Command
{
    public function __construct(
        public EventId $eventId,
        public UserId $userId,
        public string $photoPath
    )
    {}
}
