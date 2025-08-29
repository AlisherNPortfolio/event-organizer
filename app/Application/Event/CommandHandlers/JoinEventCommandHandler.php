<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\JoinEventCommand;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Domain\Event\Entities\Participant;

class JoinEventCommandHandler extends CommandHandler
{
    public function __construct(
        private IEventRepository $eventRepository,
        private IParticipantRepository $participantRepository
    )
    {}

    public function handle(JoinEventCommand $command): void
    {
        $event = $this->eventRepository->findById($command->eventId);

        throw_if(!$event, "Tadbir topilmadi");
        throw_if(
            $this->participantRepository->exists($command->eventId, $command->userId),
            "Siz allaqachon tadbirga qo'shilgansiz"
        );

        $event->join($command->userId);
        $newParticipant = new Participant($command->eventId, $command->userId);
        $this->participantRepository->save($newParticipant);
        $this->eventRepository->save($event);
    }
}
