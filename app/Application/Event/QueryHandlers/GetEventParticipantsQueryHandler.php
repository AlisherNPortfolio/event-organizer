<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventParticipantsQuery;
use App\Application\RepositoryInterfaces\IParticipantRepository;

class GetEventParticipantsQueryHandler
{
    public function __construct(
        private readonly IParticipantRepository $participantRepository
    )
    {}

    public function handle(GetEventParticipantsQuery $query): array
    {
        $participants = $this->participantRepository->findByEvent($query->eventId);

        $participants =  array_map(function ($participant) {
            $userData = $participant->getUserData();
            return [
                'user' => [
                    'id' => $userData['id'],
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'avatar' => $userData['avatar'],
                    'avatar_url' => $userData['avatar'] ? asset('storage/' . $userData['avatar']) : null,
                    'rating' => $userData['rating'],
                ],
                'participant' => [
                    'attended' => $participant->isAttended(),
                    'marked' => $participant->isMarked(),
                    'joined_at' => $participant->getJoinedAt()->format('Y-m-d H:i:s')
                ]
            ];
        }, $participants);

        $stats = [
            'total' => count($participants),
            'attended' => count(array_filter($participants, fn($p) => $p['participant']['attended'])),
            'not_attended' => count(array_filter($participants, fn($p) => !$p['participant']['attended'])),
            'pending' => count(array_filter($participants, fn($p) => !$p['participant']['marked']))
        ];

        return [
            'participants' => $participants,
            'stats' => $stats
        ];
    }
}
