<?php

namespace App\Application\Profile\Commands;

use App\Application\Bus\Command;
use App\Domain\Auth\ValueObjects\UserId;

class RemoveAvatarCommand extends Command
{
    public function __construct(
        public UserId $userId
    )
    {}
}
