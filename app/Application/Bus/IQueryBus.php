<?php

namespace App\Application\Bus;

interface IQueryBus
{
    public function ask(Query $query): mixed;
    public function register(array $map): void;
}
