<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetSimilarEventsQuery;
use App\Application\Event\Services\EventService;

class GetSimilarEventsQueryHandler
{
    public function __construct(
        private readonly EventService $service
    )
    {}

    public function handle(GetSimilarEventsQuery $query): array
    {
        if (count($query->filters)) {
            return []; // TODO: filter keyin qo'shiladi
        }

        return $this->service->getSimilarEvents($query->eventId, $query->perPage);
    }
}
