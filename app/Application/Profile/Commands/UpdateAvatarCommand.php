<?php

namespace App\Application\Profile\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;

class UpdateAvatarCommand extends Command
{
    public function __construct(
        public UserId $userId,
        public string $path
    )
    {}
}
