<?php

namespace App\Application\Profile\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;

class UpdateProfileCommand extends Command
{
    public function __construct(
        public UserId $userId,
        public string $name,
        public string $email,
        public ?string $oldPassword = null,
        public ?string $newPassword = null,
        public ?string $newPasswordConfirmation = null
    )
    {}
}
