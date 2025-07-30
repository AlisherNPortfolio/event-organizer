<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventQuery;
use App\Application\Event\Services\EventService;

class GetEventQueryHandler
{
    public function __construct(
        private EventService $service
    )
    {}

    public function handle(GetEventQuery $query): ?array
    {
        $eventDTO = $this->service->getEventById($query->eventId);
        return $eventDTO ? $eventDTO->toArray() : null;
    }
}
