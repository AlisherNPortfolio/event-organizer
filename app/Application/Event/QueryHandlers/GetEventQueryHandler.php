<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventQuery;
use App\Application\Event\Services\EventService;
use App\Application\Event\Services\ParticipantService;
use App\Presentation\ViewModels\EventViewModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetEventQueryHandler
{
    public function __construct(
        private EventService $service,
        private ParticipantService $participantService
    )
    {}

    public function handle(GetEventQuery $query): array
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

        return [
            $eventDTO ?? null,
            $isParticipating,
            $participants,
            $statistics
        ];
    }
}
