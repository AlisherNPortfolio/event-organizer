<?php

namespace App\Application\Bus;

use App\Application\Bus\ICommandBus;
use Illuminate\Bus\Dispatcher;

class IlluminateCommandBus implements ICommandBus
{
    public function __construct(protected Dispatcher $bus)
    {
    }

    public function register(array $map): void
    {
        $this->bus->map($map);
    }

    public function dispatch(\App\Application\Bus\Command $command): mixed
    {
        return $this->bus->dispatch($command);
    }
}
