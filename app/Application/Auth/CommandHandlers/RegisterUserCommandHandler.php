<?php

namespace App\Application\Auth\CommandHandlers;

use App\Application\Auth\Commands\RegisterUserCommand;
use App\Application\Bus\CommandHandler;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\Factories\UserFactory;

class RegisterUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly IUserRepository $repository
    ) {}

    public function handle(RegisterUserCommand $command): string
    {
        try {
            if ($this->repository->findByEmail($command->email)) {
                throw new \Exception('Bunday email bilan foydalanuvchi ro\'yxatdan o\'tgan.');
            }

            $user = UserFactory::create(
                $command->name,
                $command->email,
                $command->password
            );

            $this->repository->save($user);

            return $user->getId()->value();
        } catch (\Exception $e) {
            $message = get_exception_message('Foydalanuvchini ro\'yxatdan o\'tkazishda xatolik.', $e);
            throw new \RuntimeException($message);
        }
    }
}
