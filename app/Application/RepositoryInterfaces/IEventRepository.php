<?php

namespace App\Application\RepositoryInterfaces;

use App\Domain\Event\Entities\Event;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventStatus;

interface IEventRepository
{
    public function save(Event $event): void;
    public function findById(EventId $id): ?Event;
    public function findByOrganizer(UserId $organizerId): array;
    public function findAll(int $page = 1, int $perPage = 10): array;
    public function search(string $query, array $filters = []): array;
    public function delete(EventId $id): void;
    public function findSimilarEvents(Event $event, int $limit = 6): array;

    public function updateStatus(EventId $eventId, EventStatus $status): bool;
}
