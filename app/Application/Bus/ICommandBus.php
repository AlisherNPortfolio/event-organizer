<?php

namespace App\Bus;

interface ICommandBus
{
    public function dispatch(Command $command): mixed;
    public function register(array $map): void;
}
