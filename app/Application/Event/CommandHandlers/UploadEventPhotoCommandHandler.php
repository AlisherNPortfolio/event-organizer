<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\UploadEventPhotoCommand;
use App\Application\RepositoryInterfaces\IEventPhotoRepository;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Domain\Event\Entities\EventPhoto;
use InvalidArgumentException;

class UploadEventPhotoCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IEventPhotoRepository $eventPhotoRepository
    )
    {}

    public function handle(UploadEventPhotoCommand $command): EventPhoto
    {
        $event = $this->eventRepository->findById($command->eventId);
        throw_if(
            !$event,
            new InvalidArgumentException("Tadbir topilmadi")
        );

        $eventPhotoEntity = new EventPhoto($command->eventId, $command->userId, $command->photoPath);
        return $this->eventPhotoRepository->savePhoto($eventPhotoEntity, $event);
    }
}
