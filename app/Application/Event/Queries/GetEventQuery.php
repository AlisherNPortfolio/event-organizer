<?php

namespace App\Application\Event\Queries;

use App\Application\Bus\Query;
use App\Domain\Event\ValueObjects\EventId;

class GetEventQuery extends Query
{
    public function __construct(
        public readonly EventId $eventId
    )
    {}
}
