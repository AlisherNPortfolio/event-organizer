<?php

namespace App\Application\Event\Queries;

use App\Application\Bus\Query;
use App\Domain\Event\ValueObjects\EventId;

class GetSimilarEventsQuery extends Query
{
    public function __construct(
        public EventId $eventId,
        public int $page = 1,
        public int $perPage = 4,
        public ?string $search = null,
        public array $filters = []
    )
    {}
}
