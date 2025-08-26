<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventQuery;
use App\Application\Event\Services\EventService;
use App\Application\Event\Services\ParticipantService;
use Illuminate\Support\Facades\Auth;

class GetEventQueryHandler
{
    public function __construct(
        private EventService $service,
        private ParticipantService $participantService
    )
    {}

    public function handle(GetEventQuery $query, bool $onlyEventData = false): array
    {
        $eventDTO = $this->service->getEventById($query->eventId);
        abort_if(!$eventDTO, 404, "Tadbir topilmadi");

        $isParticipating = false;
        if (Auth::check()) {
            $isParticipating = $this->participantService
                                    ->isUserParticipating(
                                        $query->eventId->value(),
                                        Auth::id()
                                    );
        }

        $participants = [];
        if (Auth::check() && $eventDTO->organizerId == Auth::id()) {
            $participants = $this->participantService->getEventParticipants($query->eventId->value());
        }

        $statistics = $this->participantService->getParticipantStatistics($query->eventId->value());

        $result = [$eventDTO ?? null];
        if (!$onlyEventData) {
            $result = array_merge($result, [$isParticipating, $participants, $statistics]);
        }

        return $result;
    }
}
