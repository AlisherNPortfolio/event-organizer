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

    public function getUserParticipants(string $userId, ?string $status = null): array
    {
        $participants = $this->participantRepository->findByUser(new UserId($userId));
        $result = [];

        foreach ($participants as $participant) {
            $event = $this->eventRepository->findById($participant->getEventId());
            if (!$event) continue;

            if($status && $event->getStatus() !== $status) continue;

            $organizer = $this->userRepository->findById($event->getOrganizerId());

            $result[] = [
                'participant' => $this->mapParticipantToDTO($participant),
                'event' => $this->mapEventToDTO($event, $organizer),
                'joined_at' => $participant->getJoinedAt(),
                'attended' => $participant->isAttended(),
                'marked' => $participant->isMarked()
            ];
        }

        usort($result, function ($a, $b) {
                return $b['event']['start_time'] <=> $a['event']['start_time'];
            });

        return $result;
    }

    public function getUserAttandanceHistory(string $userId): array
    {
        $participants = $this->participantRepository->findByUser(new UserId($userId));
        $user = $this->userRepository->findById(new UserId($userId));

        throw_if(!$user, new InvalidArgumentException("Foydalanuvchi topilmadi"));

        $history = [];
        $currentRating = $user->getRating();

        foreach ($participants as $participant) {
            $event = $this->eventRepository->findById($participant->getEventId());
            if (!$event || $event->getStatus() !== 'completed' || !$participant->isMarked()) continue;

            $ratingChange = 0;
            $reason = '';

            if (!$participant->isAttended()) {
                $ratingChange = -1;
                $reason = "Tadbirda qatnashmadi";
            }

            $history[] = [
                'event_id' => $event->getId()->value(),
                'event_title' => $event->getTitle()->value(),
                'event_date' => $event->getStartTime(),
                'attended' => $participant->isAttended(),
                'rating_change' => $ratingChange,
                'reason' => $reason,
                'marked_at' => $participant->getJoinedAt()
            ];
        }

        usort($history, function ($a, $b) {
            return $b['event_date'] <=> $a['event_date'];
        });

        return [
            'current_rating' => $currentRating,
            'history' => $history,
            'total_penalties' => count(array_filter($history, fn($h) => $h['rating_change'] < 0))
        ];
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

    private function mapEventToDTO($event, $organizer = null): array
    {
        $organizerName = $organizer ? $organizer->getName() : "Noma'lum";

        return [
            'id' => $event->getId()->value(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'address' => $event->getAddress(),
            'start_time' => $event->getStartTime(),
            'end_time' => $event->getEndTime(),
            'status' => $event->getStatus(),
            'organizer_name' => $organizerName,
            'price' => $event->getPrice()->getAmount(),
            'currency' => $event->getPrice()->getCurrency(),
            'is_free' => $event->getPrice()->isFree(),
            'image' => $event->getImage(),
            'photos' => $event->getPhotos(),
            'max_participants' => $event->getParticipantLimit()->getMax(),
            'min_participants' => $event->getParticipantLimit()->getMin()
        ];
    }
}
