<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\CreateEventCommand;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Event\Factories\EventFactory;
use DateTime;
use InvalidArgumentException;

class CreateEventCommandHandler extends CommandHandler
{
    public function __construct(
        private IEventRepository $eventRepository,
        private IUserRepository $userRepository
    )
    {}

    public function handle(CreateEventCommand $command): string
    {
        $organizer = $this->userRepository->findById($command->organizerId);
        throw_if(!$organizer, new InvalidArgumentException("Tadbir yaratuvchisi topilmadi"));

        $startTime = new DateTime($command->startTime);
        $endTime = $command->endTime ? new DateTime($command->endTime) : null;

        $event = EventFactory::create(
            $command->organizerId,
            $command->title,
            $command->description,
            $command->address,
            $command->minParticipants,
            $command->maxParticipants,
            $command->price,
            $startTime,
            $endTime,
            $command->currency,
            $command->images
        );

        $this->eventRepository->save($event);

        return $event->getId()->value();
    }
}
