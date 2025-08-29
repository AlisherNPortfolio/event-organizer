<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\LeaveEventCommand;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\Shared\Exceptions\EventException;
use App\Application\Shared\Exceptions\EventNotFoundException;
use App\Domain\Auth\ValueObjects\UserId;
use Exception;
use Illuminate\Support\Facades\Auth;

class LeaveEventCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IEventRepository $eventRepository
    )
    {}

    public function handle(LeaveEventCommand $command): bool
    {
        $event = $this->eventRepository->findById($command->eventId);
        throw_if(!$event, new EventNotFoundException());
        throw_if(
            $event->getStatus() !== 'upcoming',
            new EventException("Faqat boshlanmagan tadbirlardan chiqib ketish mumkin")
        );
        $isUserParticipating = $event->hasParticipant(
            new UserId(Auth::id())
        );

        throw_if(!$isUserParticipating, new EventException("Siz bu tadbirda ishtirok etmayapsiz"));

        return $this->eventRepository->removeParticipant($event->getId(), new UserId(Auth::id()));
    }
}
