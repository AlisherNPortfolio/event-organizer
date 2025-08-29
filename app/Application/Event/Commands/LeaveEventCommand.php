<?php

namespace App\Application\Event\Commands;

use App\Application\Bus\Command;
use App\Domain\Event\ValueObjects\EventId;

class LeaveEventCommand extends Command
{
    public function __construct(
        public EventId $eventId
    )
    {}
}
