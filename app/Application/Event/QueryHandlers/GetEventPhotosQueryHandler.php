<?php

namespace App\Application\Event\QueryHandlers;

use App\Application\Event\Queries\GetEventPhotosQuery;
use App\Application\RepositoryInterfaces\IEventPhotoRepository;
use App\Domain\Event\Entities\EventPhoto;

class GetEventPhotosQueryHandler
{
    public function __construct(
        private readonly IEventPhotoRepository $eventPhotoRepository
    )
    {}

    public function handle(GetEventPhotosQuery $query): array
    {
        $eventPhotos = $this->eventPhotoRepository->findByEventId($query->eventId);
        return array_map(function (EventPhoto $photo) {
                    return [
                                'id' => $photo->getId(),
                                'event_id' => $photo->getEventId()->value(),
                                'path' => $photo->getPath(),
                                'full_url' => asset('storage/' . $photo->getPath()),
                                'uploaded_at' => $photo->getUploadedAt()?->format('Y-m-d H:i:s'),
                                'uploaded_by' => [
                                    'id' => $photo->getUploader()?->getUserData()['id'],
                                    'name' => $photo->getUploader()?->getUserData()['name'] ?? "Noma'lum",
                                    'avatar_url' => $photo->getUploader()?->getUserData()['avatar'] ? asset($photo->getUploader()?->getUserData()['avatar']) : null
                                ]
                            ];
        }, $eventPhotos);
    }
}
