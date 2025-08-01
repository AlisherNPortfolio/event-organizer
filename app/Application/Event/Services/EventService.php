<?php

namespace App\Application\Event\Services;

use App\Application\Event\DTO\EventDTO;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Event\Entities\Event;
use App\Domain\Event\ValueObjects\EventId;

class EventService
{
    public function __construct(
        private IEventRepository $eventRepository,
        private IUserRepository $userRepository,
        private IParticipantRepository $participantRepository
    )
    {}

    public function getEventById(EventId $eventId): ?EventDTO
    {
        $event = $this->eventRepository->findById($eventId);

        if (!$event) return null;

        return $this->mapEventToDTO($event);
    }

    public function searchEvents(string $query, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $events = $this->eventRepository->search($query, $filters, $page, $perPage);

        return array_map(function ($event) {
            return $this->mapEventToDTO($event);
        }, $events);
    }

    public function getAllEvents(int $page = 1, int $perPage = 10): array
    {
        $events = $this->eventRepository->findAll($page, $perPage);

        return array_map(function ($event) {
            return $this->mapEventToDTO($event);
        }, $events);
    }

    private function mapEventToDTO(Event $event): EventDTO
    {
        $organizer = $this->userRepository->findById($event->getOrganizerId());
        $organizerName = $organizer ? $organizer->getName() : "Noma'lum";

        $participants = $this->participantRepository->findByEvent($event->getId());
        $currentParticipants = count($participants);

        return new EventDTO(
            $event->getId()->value(),
            $event->getOrganizerId()->value(),
            $organizerName,
            $event->getTitle()->value(),
            $event->getDescription()->value(),
            $event->getAddress(),
            $event->getParticipantLimit()->getMin(),
            $event->getParticipantLimit()->getMax(),
            $currentParticipants,
            $event->getPrice()->getAmount(),
            $event->getPrice()->getCurrency(),
            $event->getPrice()->isFree(),
            $event->getImages(),
            $event->getStatus(),
            $event->getCreatedAt(),
            $event->getStartTime(),
            $event->getEndTime()
        );
    }
}
