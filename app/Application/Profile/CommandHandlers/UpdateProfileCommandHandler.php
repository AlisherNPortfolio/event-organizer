<?php

namespace App\Application\Profile\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Profile\Commands\UpdateProfileCommand;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use DateTime;
use InvalidArgumentException;

class UpdateProfileCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IUserRepository $userRepository
    )
    {}

    public function handle(UpdateProfileCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        throw_if(
            !$user,
            new InvalidArgumentException("Foydalanuvchi topilmadi")
        );

        $userEmail = new UserEmail($command->email);
        if (!$user->getEmail()->equals($userEmail)) {
            $existingUser = $this->userRepository->findByEmail($userEmail);
            throw_if(
                $existingUser && !$existingUser->getId()->equals($user->getId()),
                new InvalidArgumentException("Bu pochta manzili tizimda mavjud")
            );
        }

        $user = User::fromDatabase(
            $user->getId(),
            $command->name,
            new UserEmail($command->email),
            $user->getPassword(),
            $user->getRating(),
            $user->getCreatedAt(),
            new DateTime()
        );

        if ($command->oldPassword && $command->newPassword) {
            throw_if(
                !$user->verifyPassword($command->oldPassword),
                new InvalidArgumentException("Joriy parol noto'g'ri")
            );

            throw_if(
                $command->newPassword !== $command->newPasswordConfirmation,
                new InvalidArgumentException("Yangi parol tasdiqlash paroli bilan mos kelmadi")
            );

            $user->updatePassword(
                new Password($command->newPassword)
            );
        }

        $this->userRepository->save($user);
    }
}
