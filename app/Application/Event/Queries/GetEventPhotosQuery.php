<?php

namespace App\Application\Event\Queries;

use App\Application\Bus\Query;
use App\Domain\Event\ValueObjects\EventId;

class GetEventPhotosQuery extends Query
{
    public function __construct(
        public EventId $eventId
    )
    {}
}
