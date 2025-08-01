<?php

namespace App\Application\Event\Services;

use App\Application\Event\DTO\ParticipantDTO;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\Entities\Participant;
use App\Domain\Event\ValueObjects\EventId;
use InvalidArgumentException;

class ParticipantService
{
    public function __construct(
        private IParticipantRepository $participantRepository,
        private IEventRepository $eventRepository,
        private IUserRepository $userRepository
    )
    {}

    public function isUserParticipating(string $eventId, string $userId): bool
    {
        return $this->participantRepository->exists(
            new EventId($eventId),
            new UserId($userId)
        );
    }

    public function getEventParticipants(string $eventId): array
    {
        $eventId = new EventId($eventId);
        $event = $this->eventRepository->findById($eventId);
        throw_if(!$event, new InvalidArgumentException("Tadbir topilmadi"));

        $participants = $this->participantRepository->findByEvent($eventId);

        $result = [];
        foreach ($participants as $participant) {
            $user = $this->userRepository->findById($participant->getUserId());
            if (!$user) continue;

            $result[] = [
                'participant' => $this->mapParticipantToDTO($participant),
                'user' => $this->mapUserToDTO($user),
                'joined_at' => $participant->getJoinedAt(),
                'attended' => $participant->isAttended(),
                'marked' => $participant->isMarked()
            ];
        }

        usort($result, fn ($a, $b) => $b['event']['start_time'] <=> $a['event']['start_time']);

        return $result;
    }

    public function getParticipantStatistics(string $eventId): array
    {
        $eventId = new EventId($eventId);
        $event = $this->eventRepository->findById($eventId);
        throw_if(!$event, new InvalidArgumentException("Tadbir topilmadi"));

        $participants = $this->participantRepository->findByEvent($eventId);

        $totalParticipants = count($participants);
        $attendedCount = 0;
        $markedCount = 0;
        $notMarkedCount = 0;

        foreach ($participants as $participant) {
            if ($participant->isMarked()) {
                $markedCount++;
                if ($participant->isAttended()) {
                    $attendedCount++;
                }
            } else {
                $notMarkedCount++;
            }
        }

        $attendanceRate = $markedCount > 0 ? ($attendedCount / $markedCount) * 100 : 0;
        $fillRate = $event->getParticipantLimit()->getMax() > 0
                    ? ($totalParticipants / $event->getParticipantLimit()->getMax()) * 100
                    : 0;

        return [
            'total_participants' => $totalParticipants,
            'attended_count' => $attendedCount,
            'not_attended_count' => $markedCount - $attendedCount,
            'marked_count' => $markedCount,
            'not_marked_count' => $notMarkedCount,
            'attendance_rate' => round($attendanceRate, 2),
            'fill_rate' => round($fillRate, 2),
            'max_participants' => $event->getParticipantLimit()->getMax(),
            'min_participants' => $event->getParticipantLimit()->getMin(),
            'is_full' => $totalParticipants >= $event->getParticipantLimit()->getMax(),
            'meets_minimum' => $totalParticipants >= $event->getParticipantLimit()->getMin(),
            'can_start' => $totalParticipants >= $event->getParticipantLimit()->getMin(),
            'pending_marking' => $notMarkedCount
        ];
    }

    public function mapParticipantToDTO(Participant $participant): ParticipantDTO
    {
        return new ParticipantDTO(
            $participant->getEventId()->value(),
            $participant->getUserId()->value(),
            $participant->getJoinedAt(),
            $participant->isAttended(),
            $participant->isMarked()
        );
    }

    private function mapUserToDTO(User $user): array
    {
        return [
            'id' => $user->getId()->value(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'rating' => $user->getRating()
        ];
    }
}
