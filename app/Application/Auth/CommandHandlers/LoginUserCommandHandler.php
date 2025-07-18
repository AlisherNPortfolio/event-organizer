<?php

namespace App\Application\Auth\CommandHandlers;

use App\Application\Auth\Commands\LoginUserCommand;
use App\Application\Auth\Services\AuthService;
use App\Application\Bus\CommandHandler;
use Illuminate\Support\Facades\Auth;

class LoginUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly AuthService $service
    )
    {}

    public function handle(LoginUserCommand $command): ?string
    {
        // $user = $this->service->authenticate($command->email, $command->password);

        if (!Auth::attempt([
            'email' => $command->email->value(),
            'password' => $command->password->plainValue()
        ], $command->remember)) {
            return null;
        }

        return Auth::id();
    }
}
