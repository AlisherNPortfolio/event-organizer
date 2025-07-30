<?php

namespace App\Application\RepositoryInterfaces;

use App\Domain\Event\Entities\Participant;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Auth\ValueObjects\UserId;

interface IParticipantRepository
{
    public function save(Participant $participant): void;
    public function findByEventAndUser(EventId $eventId, UserId $userId): ?Participant;
    public function findByEvent(EventId $eventId): array;
    public function findByUser(UserId $userId): array;
    public function exists(EventId $eventId, UserId $userId): bool;
    public function delete(EventId $eventId, UserId $userId): void;
}
