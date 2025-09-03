<?php

namespace App\Infrastructure\Repositories;

use App\Application\RepositoryInterfaces\IEventRepository;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\Entities\Event as DomainEvent;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventPrice;
use App\Domain\Event\ValueObjects\EventStatus;
use App\Domain\Event\ValueObjects\EventTitle;
use App\Domain\Event\ValueObjects\ParticipantLimit;
use App\Infrastructure\Models\Event as EloquentEvent;
use App\Infrastructure\Models\Participant;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventRepository implements IEventRepository
{
    public function save(DomainEvent $event, bool $isEdit = false): void
    {
        DB::beginTransaction();
        try {
            $eloquentEvent = EloquentEvent::find($event->getId()->value());

            if (!$eloquentEvent) {
                $eloquentEvent = new EloquentEvent();
                $eloquentEvent->id = $event->getId()->value();
                $eloquentEvent->created_at = $event->getCreatedAt();
            }

            $eloquentEvent->organizer_id = $event->getOrganizerId()->value();
            $eloquentEvent->title = $event->getTitle()->value();
            $eloquentEvent->description = $event->getDescription()->value();
            $eloquentEvent->address = $event->getAddress();
            $eloquentEvent->start_time = $event->getStartTime();
            $eloquentEvent->end_time = $event->getEndTime();
            $eloquentEvent->min_participants = $event->getParticipantLimit()->getMin();
            $eloquentEvent->max_participants = $event->getParticipantLimit()->getMax();
            $eloquentEvent->price = $event->getPrice()->getAmount();
            $eloquentEvent->currency = $event->getPrice()->getCurrency();
            $eloquentEvent->is_free = $event->getPrice()->isFree();
            if ($isEdit) {
                $eloquentEvent->photos = $event->getPhotos();
                $eloquentEvent->participants = $event->getParticipants();
            }
            $eloquentEvent->image = $event->getImage();
            $eloquentEvent->status = $event->getStatus();
            $eloquentEvent->updated_at = new DateTime();

            $eloquentEvent->save();

            $this->syncParticipants($event);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function findById(EventId $id): ?DomainEvent
    {
        $eloquentEvent = EloquentEvent::query()
                            ->with(['participants.user'])
                            ->find($id->value());

        if (!$eloquentEvent) {
            return null;
        }

        return $this->toDomainEvent($eloquentEvent);
    }

    public function findByOrganizer(UserId $organizerId): array
    {
        $eloquentEvents = EloquentEvent::query()
                            ->with(['participants'])
                            ->where('organizer_id', $organizerId->value())
                            ->orderBy('created_at', 'desc')
                            ->get();

        return $eloquentEvents->map(function ($eloquentEvent) {
            return $this->toDomainEvent($eloquentEvent);
        })->toArray();
    }

    public function findAll(int $page = 1, int $perPage = 10): array
    {
        $eloquentEvents = EloquentEvent::query()
                            ->with(['participants'])
                            ->upcoming()
                            ->orderBy('created_at', 'desc')
                            ->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->get();

        return $eloquentEvents->map(function ($eloquentEvent) {
            return $this->toDomainEvent($eloquentEvent);
        })->toArray();
    }

    public function search(string $query, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $queryBuilder = EloquentEvent::query()->with(['participants']);

        if (!empty($query)) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%");
            });
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        if (isset($filters['is_free']) && !empty($filters['is_free'])) {
            $queryBuilder->where('is_free', $filters['is_free']);
        }

        if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $queryBuilder->where('start_time', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && !empty($filters['end_date'])) {
            $queryBuilder->where('start_time', '<=', $filters['end_date']);
        }

        if (isset($filters['min_price']) && !empty($filters['min_price'])) {
            $queryBuilder->where('price', '>=',  $filters['min_price']);
        }

        if (isset($filters['max_price']) && !empty($filters['max_price'])) {
            $queryBuilder->where('price', '<=', $filters['max_price']);
        }

        $eloquentEvents = $queryBuilder->where('status', '!=', 'completed')
                                    ->orderBy('start_time', 'asc')
                                    ->skip(($page - 1) * $perPage)
                                    ->take($perPage)
                                    ->get();

        return $eloquentEvents->map(function ($eloquentEvent) {
            return $this->toDomainEvent($eloquentEvent);
        })->toArray();
    }

    public function findSimilarEvents(DomainEvent $event, int $limit = 6): array
    {
        $eventTitle = $event->getTitle()->value();
        $eventAddress = $event->getAddress();
        $eventPrice = $event->getPrice()->getAmount();
        $isFree = $event->getPrice()->isFree();
        $currentEventId = $event->getId()->value();

        $titleKeywords = $this->extractKeywords($eventTitle);

        $queryBuilder = EloquentEvent::query()
                            ->where('id', '!=', $currentEventId)
                            ->upcoming();

        $queryBuilder->selectRaw('*, (
            CASE
                WHEN is_free = ? THEN 20
                ELSE 0
            END +
            CASE
                WHEN ABS(price - ?) <= 10000 THEN 15
                WHEN ABS(price - ?) <= 5000 THEN 10
                WHEN ABS(price - ?) <= 100000 THEN 5
                ELSE 0
            END +
            CASE
                WHEN LOWER(address) LIKE ? THEN 25
                ELSE 0
            END
        ) as similarity_score', [
            $isFree ? 1 : 0,
            $eventPrice,
            $eventPrice,
            $eventPrice,
            '%' . strtolower($eventAddress) . '%' // TODO: event address ni value object qilib yaratish va ishlatish
        ]);

        if (!empty($titleKeywords)) {
            $titleConditions = [];
            $titleParams = [];

            foreach ($titleKeywords as $keyword) {
                if (strlen($keyword) > 2) {
                    $titleConditions[] = 'LOWER(title) LIKE ?';
                    $titleParams[] = '%' . strtolower($keyword) . '%';
                }
            }

            if (!empty($titleConditions)) {
                $queryBuilder->whereRaw('(' . implode(' OR ', $titleConditions) . ')', $titleParams);
            }
        }

        $eloquentEvents = $queryBuilder
                            ->orderByRaw('similarity_score DESC, created_at DESC')
                            ->limit($limit)
                            ->get();

        if ($eloquentEvents->count() < $limit) {
            $remainingLimit = $limit - $eloquentEvents->count();
            $existingIds = $eloquentEvents->pluck('id')->toArray();
            $existingIds[] = $currentEventId;

            $additionalEvents = EloquentEvent::query()
                                ->whereNotIn('id', $existingIds)
                                ->upcoming()
                                ->orderBy('created_at', 'desc')
                                ->limit($remainingLimit)
                                ->get();

            $eloquentEvents = $eloquentEvents->merge($additionalEvents);
        }

        return $eloquentEvents->map(function ($eloquentEvent) {
            return $this->toDomainEvent($eloquentEvent);
        })->toArray();
    }

    public function delete(EventId $id): void
    {
        EloquentEvent::query()->where('id', $id->value())->delete();
    }

    public function updateStatus(EventId $eventId, EventStatus $status): bool
    {
        $event = $this->findById($eventId);
        abort_if(!$event, 404, "{$eventId->value()} IDli tadbir topilmadi");

        match($status->value()) {
            'ongoing' => $event->markAsOngoing(),
            'completed' => $event->markAsCompleted()
        };

        $this->save($event);

        return true;
    }

    // TODO: bu metodni ParticipantRepository ga ko'chirish kerak
    public function removeParticipant(EventId $eventId, UserId $userId): bool
    {
        try {
            Participant::query()
            ->where('event_id', $eventId->value())
            ->where('user_id', $userId->value())
            ->delete();

            return true;
        } catch (Exception $e) {
            Log::error("Tadbir ishtirokchisini o'chirib bo'lmadi. Sabab: " . $e->getMessage());
            return false;
        }
    }

    private function extractKeywords(string $title): array
    {
        $commonWords = ['va', 'yoki', 'bilan', 'uchun', 'dan', 'ga', 'ni', 'da', 'o\'yin', 'tadbir', 'klub', 'sport'];

        $words = preg_split('/[\s\-_,\.]+/', strtolower($title));
        $keywords = array_filter($words, function ($word) use ($commonWords) {
            return strlen($word) > 2 && !in_array($word, $commonWords);
        });

        return array_values($keywords);
    }

    private function syncParticipants(DomainEvent $event): void
    {
        $eventId = $event->getId()->value();

        $domainParticipants = array_map(
            fn($participant) => $participant->value(),
            $event->getParticipants()
        );

        $currentParticipants = Participant::query()
                                    ->where('event_id', $eventId)
                                    ->pluck('user_id')
                                    ->toArray();

        $toAdd = array_diff($domainParticipants, $currentParticipants);
        foreach ($toAdd as $userId) {
            Participant::query()->create([
                'event_id' => $eventId,
                'user_id' => $userId,
                'attended' => false,
                'marked' => false
            ]);
        }

        $toRemove = array_diff($currentParticipants, $domainParticipants);
        if (!empty($toRemove)) {
            Participant::query()
                ->where('event_id', $eventId)
                ->whereIn('user_id', $toRemove)
                ->delete();
        }
    }

    private function toDomainEvent(EloquentEvent $event): DomainEvent
    {
        $participants = [];
        foreach ($event->participants as $participant) {
            $participants[] = new UserId($participant->user_id);
        }

        $photos = [];
        foreach ($event->photos as $photo) {
            $photos[] = [
                'path' => $photo->path,
                'uploaded_by' => $photo->uploaded_by,
                'uploaded_at' => $photo->created_at
            ];
        }

        return DomainEvent::fromDatabase(
            new EventId($event->id),
            new UserId($event->organizer_id),
            new EventTitle($event->title),
            new EventDescription($event->description),
            $event->address,
            new ParticipantLimit(
                $event->min_participants,
                $event->max_participants
            ),
            new EventPrice($event->price, $event->currency),
            $event->status,
            $event->start_time,
            $event->end_time,
            $event->created_at,
            $event->image,
            $participants,
            $photos
        );
    }
}
