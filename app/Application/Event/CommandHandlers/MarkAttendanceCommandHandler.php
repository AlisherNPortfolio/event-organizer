<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\MarkAttendanceCommand;
use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Application\RepositoryInterfaces\IUserRepository;
use InvalidArgumentException;

class MarkAttendanceCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IParticipantRepository $participantRepository,
        private readonly IUserRepository $userRepository
    )
    {}

    public function handle(MarkAttendanceCommand $command): void
    {
        $participant = $this->participantRepository->findByEventAndUser($command->eventId, $command->participantId);

        throw_if(
            !$participant,
            new InvalidArgumentException("Qatnashchi topilmadi")
        );

        if ($command->attended) {
            $participant->markAsAttended();
        } else {
            $participant->markAsNotAttended();

            $user = $this->userRepository->findById($command->participantId);
            if ($user) {
                $user->decreaseRating();
                $this->userRepository->save($user);
            }
        }

        $this->participantRepository->save($participant);
    }
}
