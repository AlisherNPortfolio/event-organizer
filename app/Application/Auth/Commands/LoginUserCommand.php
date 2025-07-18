<?php

namespace App\Application\Auth\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;

class LoginUserCommand extends Command
{
    public function __construct(
        public readonly UserEmail $email,
        public readonly Password $password,
        public readonly bool $remember = false
    )
    {}
}
