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
    private DateTime $createdAt;
    private array $participants = [];
    private array $eventPhotos = [];

    public function __construct(
        private EventId $id,
        private UserId $organizerId,
        private EventTitle $title,
        private EventDescription $description,
        private string $address,
        private ParticipantLimit $participantLimit,
        private EventPrice $price,
        private DateTime $startTime,
        private DateTime $endTime,
        private string $image
    )
    {
        $this->createdAt = new DateTime();
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
        DateTime $endTime,
        string $image
    ): self
    {
        self::validateEventTimesForCreation($startTime, $endTime);
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
            $image
        );
    }

    public static function fromDatabase(
        EventId $id,
        UserId $organizerId,
        EventTitle $title,
        EventDescription $description,
        string $address,
        ParticipantLimit $participantLimit,
        EventPrice $price,
        string $status = 'upcoming',
        DateTime $startTime,
        DateTime $endTime,
        DateTime $createdAt,
        string $image,
        array $participants = [],
        array $photos = []
    ): self {
        $event = new self(
            $id,
            $organizerId,
            $title,
            $description,
            $address,
            $participantLimit,
            $price,
            $startTime,
            $endTime,
            $image
        );

        $event->status = $status;
        $event->createdAt = $createdAt;
        $event->participants = $participants;
        $event->eventPhotos = $photos;

        return $event;
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

    public function getImage(): string
    {
        return $this->image;
    }

    public function getPhotos(): array
    {
        return $this->eventPhotos;
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

    public function getParticipants(): array
    {
        return $this->participants;
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

    public function markAsOngoing(): void
    {
        throw_if($this->status != 'upcoming', new InvalidArgumentException("Faqat 'upcoming' holatidagi tadbirlar boshlanishi mumkin"));
        $this->status = 'ongoing';
    }

    public function markAsCompleted(): void
    {
        throw_if($this->status != 'ongoing', new InvalidArgumentException("Faqat 'ongoing' holatidagi tadbirlar tugatilishi mumkin"));

        $this->status = 'completed';
    }

    public function join(UserId $userId): void
    {
        throw_if(
            $this->isOrganizer($userId),
            new InvalidArgumentException("Tashkilotchi o'zining tadbiriga qatnashchi sifatida qo'shilishi mumkin emas")
        );

        throw_if(
            $this->hasParticipant($userId),
            new InvalidArgumentException("Siz allaqachon tadbirga qo'shilgansiz")
        );

        throw_if(
            !$this->canAcceptMoreParticipants(),
            new InvalidArgumentException("Tadbirda joy qolmadi")
        );

        throw_if(
            $this->status !== 'upcoming',
            new InvalidArgumentException("Faqat kutilayotgan tadbirga qo'shilish mumkin")
        );

        $this->participants[] = $userId;
    }

    public function hasParticipant(UserId $userId): bool
    {
        foreach ($this->participants as $participant) {
            if ($participant->equals($userId)) {
                return true;
            }
        }

        return false;
    }

    public function addPhoto(string $path, UserId $uploadedBy): void
    {
        throw_if(
            !$this->hasParticipant($uploadedBy) && !$this->isOrganizer($uploadedBy),
            new InvalidArgumentException("Faqat qatnashchilar va tashkilotchi rasm yuklashi mumkin")
        );

        throw_if(
            $this->status !== 'ongoing',
            new InvalidArgumentException("Rasm faqat tadbir bo'layotgan paytda yuklanishi mumkin")
        );

        $userPhotoCount = count($this->getPhotos());
        throw_if(
            $userPhotoCount > 5,
            new InvalidArgumentException("Bitta tadbir uchun bitta qatnashchi faqat 5 tagacha rasn yuklashi mumkin")
        );

        $this->eventPhotos[] = $path;
    }

    private function canAcceptMoreParticipants(): bool
    {
        return count($this->participants) < $this->participantLimit->getMax();
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

    public function isOrganizer(UserId $userId): bool
    {
        return $this->organizerId->equals($userId);
    }
}
