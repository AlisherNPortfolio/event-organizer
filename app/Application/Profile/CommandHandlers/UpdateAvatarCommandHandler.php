<?php

namespace App\Application\Profile\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Profile\Commands\UpdateAvatarCommand;
use App\Application\RepositoryInterfaces\IUserRepository;
use InvalidArgumentException;

class UpdateAvatarCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IUserRepository $userRepository
    )
    {}

    public function handle(UpdateAvatarCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        throw_if(
            !$user,
            new InvalidArgumentException("Foydalanuvchi topilmadi")
        );

        $user->updateAvatar($command->path);
        $this->userRepository->save($user);
    }
}
