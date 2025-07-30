<?php

namespace App\Infrastructure\Repositories;

use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\Entities\Participant as DomainParticipant;
use App\Domain\Event\ValueObjects\EventId;
use App\Infrastructure\Models\Participant as EloquentParticipant;

class ParticipantRepository implements IParticipantRepository
{
    public function save(DomainParticipant $participant): void
    {
        $eloquentParticipant = EloquentParticipant::query()->where([
            'event_id' => $participant->getEventId()->value(),
            'user_id' => $participant->getUserId()->value()
        ])->first();

        if (!$eloquentParticipant) {
            $eloquentParticipant = new EloquentParticipant();
            $eloquentParticipant->event_id = $participant->getEventId()->value();
            $eloquentParticipant->user_id = $participant->getUserId()->value();
        }

        $eloquentParticipant->attended = $participant->isAttended();
        $eloquentParticipant->marked = $participant->isMarked();

        $eloquentParticipant->save();
    }

    public function findByEventAndUser(EventId $eventId, UserId $userId): ?DomainParticipant
    {
        $eloquentParticipant = EloquentParticipant::query()->where([
            'event_id' => $eventId->value(),
            'user_id' => $userId->value()
        ])->first();

        if (!$eloquentParticipant) {
            return null;
        }

        return $this->toDomainParticipant($eloquentParticipant);
    }

    public function findByEvent(EventId $eventId): array
    {
        $eloquentParticipants = EloquentParticipant::query()
            ->with(['user', 'event'])->where('event_id', $eventId->value())
            ->orderBy('created_at', 'asc')
            ->get();

        return $eloquentParticipants->map(function ($eloquentParticipant) {
            return $this->toDomainParticipant($eloquentParticipant);
        })->toArray();
    }

    public function findByUser(UserId $userId): array
    {
        $eloquentParticipants = EloquentParticipant::query()
            ->with(['user', 'event'])
            ->where('user_id', $userId->value())
            ->orderBy('created_at', 'desc')
            ->get();

        return $eloquentParticipants->map(function ($eloquentParticipant) {
            return $this->toDomainParticipant($eloquentParticipant);
        })->toArray();
    }

    public function exists(EventId $eventId, UserId $userId): bool
    {
        return EloquentParticipant::query()->where([
            'event_id' => $eventId->value(),
            'user_id' => $userId->value()
        ])->exists();
    }

    public function delete(EventId $eventId, UserId $userId): void
    {
        EloquentParticipant::query()
        ->where([
            'event_id' => $eventId->value(),
            'user_id' => $userId->value()
        ])->delete();
    }

    private function toDomainParticipant(EloquentParticipant $eloquentParticipant): DomainParticipant
    {
        $participant = new DomainParticipant(
            new EventId($eloquentParticipant->event_id),
            new UserId($eloquentParticipant->user_id)
        );

        if ($eloquentParticipant->marked) {
            if ($eloquentParticipant->attended) {
                $participant->markAsAttended();
            } else {
                $participant->markAsNotAttended();
            }
        }

        return $participant;
    }
}
