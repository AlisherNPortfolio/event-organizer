<?php

namespace App\Infrastructure\Repositories;

use App\Application\RepositoryInterfaces\IEventPhotoRepository;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\Entities\EventPhoto as DomainEventPhoto;
use App\Domain\Event\Entities\Event as DomainEvent;
use App\Domain\Event\ValueObjects\EventId;
use App\Infrastructure\Models\EventPhoto as EloquentEventPhoto;
use DateTime;
use InvalidArgumentException;

class EventPhotoRepository implements IEventPhotoRepository
{
    public function savePhoto(DomainEventPhoto $eventPhoto, DomainEvent $event): DomainEventPhoto
    {
        throw_if(
            !$event->hasParticipant($eventPhoto->getUploadedBy()) && !$event->isOrganizer($eventPhoto->getUploadedBy()),
            new InvalidArgumentException("Faqat qatnashchilar va tashkilotchi rasm yuklashi mumkin")
        );

        throw_if(
            $event->getStatus() !== 'ongoing',
            new InvalidArgumentException("Rasm faqat tadbir bo'layotgan paytda yuklanishi mumkin")
        );

        $userPhotoCount = count($event->getPhotos());
        throw_if(
            $userPhotoCount > 5,
            new InvalidArgumentException("Bitta tadbir uchun bitta qatnashchi faqat 5 tagacha rasn yuklashi mumkin")
        );

        $eventId = $eventPhoto->getEventId()->value();
        $uploadedBy = $eventPhoto->getUploadedBy()->value();
        $path = $eventPhoto->getPath();

        $eventPhoto = EloquentEventPhoto::query()
        ->where('event_id', $eventId)
        ->where('uploaded_by', $uploadedBy)
        ->first() ?? new EloquentEventPhoto();

        $eventPhoto->event_id = $eventId;
        $eventPhoto->uploaded_by = $uploadedBy;
        $eventPhoto->path = $path;

        $eventPhoto->save();
        return $this->toDomainEventPhoto($eventPhoto);
    }

    private function toDomainEventPhoto(EloquentEventPhoto $eventPhoto)
    {
        return DomainEventPhoto::fromDatabase(
            $eventPhoto->id,
            new EventId($eventPhoto->event_id),
            new UserId($eventPhoto->uploaded_by),
            new DateTime($eventPhoto->created_at),
            $eventPhoto->path
        );
    }
}
