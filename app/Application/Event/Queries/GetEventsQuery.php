<?php

namespace App\Application\Event\Queries;

use App\Application\Bus\Query;

class GetEventsQuery extends Query
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly array $filters = [],
        public readonly ?string $search = null
    )
    {}
}
