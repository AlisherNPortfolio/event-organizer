<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;

class JoinEventCommand extends Command
{
    public function __construct(
        public readonly EventId $eventId,
        public readonly UserId $userId
    ) {}
}
