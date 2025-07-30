<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventsQuery;
use App\Application\Event\Services\EventService;

class GetEventsQueryHandler
{
    public function __construct(
        private EventService $service
    )
    {}

    public function handle(GetEventsQuery $query): array
    {
        if (count($query->filters)) {
            return $this->service->searchEvents(
                $query->search ?? '',
                $query->filters,
                $query->page,
                $query->perPage
            );
        }

        return $this->service->getAllEvents($query->page, $query->perPage);
    }
}
