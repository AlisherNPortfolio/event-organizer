<?php

namespace App\Application\Auth\CommandHandlers;

use App\Application\Auth\Commands\LoginUserCommand;
use App\Application\Auth\Services\AuthService;
use App\Application\Bus\CommandHandler;

class LoginUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly AuthService $service
    )
    {}

    public function handle(LoginUserCommand $command): ?string
    {
        $user = $this->service->authenticate($command->email, $command->password);

        if (!$user) {
            return null;
        }

        return $user->getId()->value();
    }
}
