<?php

namespace App\Application\Profile\Query;

use App\Application\Bus\Query;
use App\Domain\Auth\ValueObjects\UserId;

class GetProfileQuery extends Query
{
    public function __construct(
        public UserId $userId
    )
    {}
}
