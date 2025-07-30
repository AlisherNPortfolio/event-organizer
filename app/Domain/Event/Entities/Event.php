<?php

namespace App\Domain\Event\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventPrice;
use App\Domain\Event\ValueObjects\EventTitle;
use App\Domain\Event\ValueObjects\ParticipantLimit;
use DateTime;
use InvalidArgumentException;

class Event
{
    private string $status = 'upcoming';
    private DateTime $createdAt = new DateTime();

    public function __construct(
        private EventId $id,
        private UserId $organizerId,
        private EventTitle $title,
        private EventDescription $description,
        private string $address,
        private ParticipantLimit $participantLimit,
        private EventPrice $price,
        private DateTime $startTime,
        private ?DateTime $endTime,
        private array $images = []
    )
    {
        self::validateEventTimesForCreation($startTime, $endTime);
        self::validateImages($images);
    }

    public static function create(
        EventId $id,
        UserId $organizerId,
        EventTitle $title,
        EventDescription $description,
        string $address,
        ParticipantLimit $participantLimit,
        EventPrice $price,
        DateTime $startTime,
        ?DateTime $endTime,
        array $images = []
    ): self
    {
        return new self(
            $id,
            $organizerId,
            $title,
            $description,
            $address,
            $participantLimit,
            $price,
            $startTime,
            $endTime,
            $images
        );
    }

    public static function validateEventTimesForCreation(DateTime $startTime, ?DateTime $endTime): void
    {
        if ($startTime <= new DateTime()) {
            throw new InvalidArgumentException("Tadbirning boshlanish vaqti ayni vaqtdan keyin bo'lishi kerak");
        }

        if ($endTime && $endTime <= $startTime) {
            throw new InvalidArgumentException("Tadbirning boshlanish vaqti tugash vaqtidan oldin bo'lishi kerak");
        }
    }

    public function getId(): EventId
    {
        return $this->id;
    }

    public function getOrganizerId(): UserId
    {
        return $this->organizerId;
    }

    public function getTitle(): EventTitle
    {
        return $this->title;
    }

    public function getDescription(): EventDescription
    {
        return $this->description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function getParticipantLimit(): ParticipantLimit
    {
        return $this->participantLimit;
    }

    public function getPrice(): EventPrice
    {
        return $this->price;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function hasStarted(): bool
    {
        return new DateTime() >= $this->startTime;
    }

    public function hasEnded(): bool
    {
        if (!$this->endTime) {
            return false;
        }

        return new DateTime() > $this->endTime;
    }

    private function validateImages(array $images): void
    {
        if (count($images) < 1) {
            throw new InvalidArgumentException("Tadbirda kamida 1 ta rasm bo'lishi kerak");
        }

        if (count($images) > 5) {
            throw new InvalidArgumentException("Tadbirda ko'pi bilan 5 ta rasm bo'lishi mumkin");
        }
    }

    private function isOrganizer(UserId $userId): bool
    {
        return $this->organizerId->equals($userId);
    }
}
