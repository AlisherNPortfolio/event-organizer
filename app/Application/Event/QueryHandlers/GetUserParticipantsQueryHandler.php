<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetUserParticipantsQuery;
use App\Application\Event\Services\ParticipantService;

class GetUserParticipantsQueryHandler
{
    public function __construct(
        private readonly ParticipantService $participantService
    )
    {}

    public function handle(GetUserParticipantsQuery $query): array
    {
        return $this->participantService->getUserParticipants($query->eventId->value());
    }
}
