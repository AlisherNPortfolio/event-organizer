<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\EditEventCommand;
use App\Application\Event\DTO\EventDTO;
use App\Application\Event\Services\EventService;

class EditEventCommandHandler extends CommandHandler
{
    public function __construct(
        private EventService $service
    )
    {}

    public function handle(EditEventCommand $command): ?EventDTO
    {
        return $this->service->getEventById($command->eventId);
    }
}
