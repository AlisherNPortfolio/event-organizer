<?php

namespace App\Application\Profile\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Profile\Commands\UpdateAvatarCommand;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Application\Shared\Services\FileManagerService;
use InvalidArgumentException;

class UpdateAvatarCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly FileManagerService $fileManagerService
    )
    {}

    public function handle(UpdateAvatarCommand $command): void
    {
        $newAvatarPath = $this->fileManagerService->upload($command->file, 'avatars');
        if ($newAvatarPath) {
            if ($command->oldPath) {
                $this->fileManagerService->remove($command->oldPath);
            }

            $user = $this->userRepository->findById($command->userId);

            throw_if(
                !$user,
                new InvalidArgumentException("Foydalanuvchi topilmadi")
            );

            $user->updateAvatar($newAvatarPath);
            $this->userRepository->save($user);
        }
    }
}
