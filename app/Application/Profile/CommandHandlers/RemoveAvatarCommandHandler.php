<?php

namespace App\Application\Profile\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Profile\Commands\RemoveAvatarCommand;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Application\Shared\Services\FileManagerService;
use InvalidArgumentException;

class RemoveAvatarCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly FileManagerService $fileManagerService
    )
    {}

    public function handle(RemoveAvatarCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);
        throw_if(
            !$user,
            new InvalidArgumentException("Foydalanuvchi topilmadi")
        );

        if ($this->fileManagerService->remove($user->getAvatar())) {
            $user->removeAvatar();
            $this->userRepository->save($user);
        }
    }
}
