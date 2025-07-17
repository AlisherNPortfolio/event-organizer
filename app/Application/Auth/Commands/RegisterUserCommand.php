<?php

namespace App\Application\Auth\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;

class RegisterUserCommand extends Command
{
    public function __construct(
        public readonly string $name,
        public readonly UserEmail $email,
        public readonly Password $password,
        public readonly string $passwordConfirmation
    )
    {}
}
