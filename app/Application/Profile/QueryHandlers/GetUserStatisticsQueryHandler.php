<?php

namespace App\Application\Profile\QueryHandlers;

use App\Application\Event\Services\EventService;
use App\Application\Event\Services\ParticipantService;
use App\Application\Profile\Query\GetUserStatisticsQuery;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IParticipantRepository;

class GetUserStatisticsQueryHandler
{
    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IParticipantRepository $participantRepository,
        private readonly EventService $eventService,
        private readonly ParticipantService $participantService
    )
    {}

    public function handle(GetUserStatisticsQuery $query): array
    {
        $organizedEvents = $this->eventService->getUserEvents($query->userId->value()); // $this->eventRepository->findByOrganizer($query->userId);
        $participants = $this->participantService->getUserParticipants($query->userId->value()); //$this->participantRepository->findByUser($query->userId);

        $upcomingEvents = array_filter($organizedEvents, fn($event) => $event->status == 'upcoming');
        $completedEvents = array_filter($organizedEvents, fn($event) => $event->status == 'completed');
        $attendedParticipants = array_filter($participants, fn($p) => $p['participant']->attended);

        $totalParticipants = array_sum(array_map(fn($event) => $event->current_participants, $organizedEvents));

        usort($organizedEvents, fn($a, $b) => $b->created_at <=> $a->created_at);
        $recentOrganized = array_slice($organizedEvents, 0, $query->limit);

        usort($participants, fn($a, $b) => $b->getJoinedAt() <=> $a->getJoinedAt());
        $recentParticipants = array_slice($participants, 0, $query->limit);

        return [
            'organized_events_total' => count($organizedEvents),
            'organized_events_upcoming' => count($upcomingEvents),
            'organized_events_completed' => count($completedEvents),
            'participated_events_total' => count($participants),
            'participant_events_attended' => count($attendedParticipants),
            'total_participants_attracted' => $totalParticipants,
            'attendance_rate' => count($participants) > 0 ? (count($attendedParticipants) / count($participants)) * 100 : 0,
            'recent_organized' => $recentOrganized,
            'recent_participants' => $recentParticipants,
            'organized_events' => $organizedEvents,
            'participants' => $participants
        ];
    }
}
