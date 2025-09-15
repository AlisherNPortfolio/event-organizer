<?php

namespace App\Application\Event\Queries;

use App\Application\Bus\Query;
use App\Domain\Auth\ValueObjects\UserId;

class GetUserParticipantsQuery extends Query
{
    public function __construct(
        public readonly UserId $eventId
    )
    {}
}
