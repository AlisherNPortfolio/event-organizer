<?php

namespace App\Application\Event\CommandHandlers;

use App\Application\Bus\CommandHandler;
use App\Application\Event\Commands\EditEventCommand;
use App\Application\Event\DTO\EventDTO;
use App\Application\Event\Services\EventService;
use App\Application\RepositoryInterfaces\IEventRepository;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\Entities\Event;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventPrice;
use App\Domain\Event\ValueObjects\ParticipantLimit;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EditEventCommandHandler extends CommandHandler
{
    public function __construct(
        private EventService $service,
        private IEventRepository $eventRepository
    )
    {}

    public function handle(EditEventCommand $command): ?EventDTO
    {
        // $event = $this->service->getEventById($command->eventId);
        $event = $this->eventRepository->findById($command->eventId);

        abort_if(!$event, Response::HTTP_NOT_FOUND, "Tadbir topilmadi");
        abort_if($event->getOrganizerId()->equals(new UserId(Auth::id())), Response::HTTP_FORBIDDEN, "Sizda bu tadbirni tahrirlash huquqi yo'q");
        abort_if($event->getStatus() !== "upcoming", 1001, "Faqat kutilayotgan tadbirlarni tahrirlash mumkin");

        $images = $event->getImages();

        if ($command->images) {
            if (!empty($images)) {
                foreach($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $images = [];
            foreach ($command->images as $image) {
                $images[] = $image->store("events", "public");
            }
        }

        $domainEvent = Event::fromDatabase(
            $command->eventId,
            $event->getOrganizerId(),
            $command->title,
            $command->description,
            $command->address,
            new ParticipantLimit(
                $command->minParticipants,
                $command->maxParticipants
            ),
            new EventPrice(
                $command->price,
                $command->currency
            ),
            $images,
            $event->getStatus(),
            new DateTime($command->startTime),
            new DateTime($command->endTime),
            $event->getCreatedAt()
        );

        $this->eventRepository->save($domainEvent);


    }
}
