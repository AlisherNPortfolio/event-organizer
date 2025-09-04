<?php

namespace App\Application\Profile\Query;

use App\Application\Bus\Query;
use App\Domain\Auth\ValueObjects\UserId;

class GetUserStatisticsQuery extends Query
{
    public function __construct(
        public UserId $userId,
        public int $limit
    )
    {}
}
