<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;

class MarkAttendanceCommand extends Command
{
    public function __construct(
        public EventId $eventId,
        public UserId $userId,
        public UserId $participantId,
        public bool $attended
    )
    {}
}
