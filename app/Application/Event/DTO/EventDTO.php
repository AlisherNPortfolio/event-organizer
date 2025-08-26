<?php

namespace App\Application\Event\DTO;

use DateTime;

class EventDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizerId,
        public readonly string $organizerName,
        public readonly string $title,
        public readonly string $description,
        public readonly string $address,
        public readonly int $minParticipants,
        public readonly int $maxParticipants,
        public readonly int $currentParticipants,
        public readonly float $price,
        public readonly string $currency,
        public readonly bool $isFree,
        public readonly string $image,
        public readonly string $status,
        public readonly DateTime $createdAt,
        public readonly DateTime $startTime,
        public readonly ?DateTime $endTime,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'organizer_id' => $this->organizerId,
            'organizer_name' => $this->organizerName,
            'title' => $this->title,
            'description' => $this->description,
            'address' => $this->address,
            'min_participants' => $this->minParticipants,
            'max_participants' => $this->maxParticipants,
            'current_participants' => $this->currentParticipants,
            'price' => $this->price,
            'currency' => $this->currency,
            'is_free' => $this->isFree,
            'image' => $this->image,
            'status' => $this->status,
            'start_time' => $this->startTime->format('Y-m-d H:i:s'),
            'end_time' => $this->endTime?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    public function getFormattedStartTime(): string
    {
        return $this->startTime->format('d.m.Y H:i');
    }

    public function getFormattedEndTime(): ?string
    {
        return $this->endTime?->format('d.m.Y H:i');
    }

    public function getMinParticipants(): int
    {
        return $this->minParticipants;
    }

    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    public function getCurrentParticipants(): int
    {
        return $this->currentParticipants;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }

    public function getOrganizerName(): string
    {
        return $this->organizerName;
    }

    public function getIsFree(): bool
    {
        return $this->isFree;
    }

    public function __get($name)
    {
        return match($name) {
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'min_participants' => $this->minParticipants,
            'max_participants' => $this->maxParticipants,
            'current_participants' => $this->currentParticipants,
            'organizer_id' => $this->organizerId,
            'organizer_name' => $this->organizerName,
            'is_free' => $this->isFree,
            'created_at' => $this->createdAt,
            default => null
        };
    }

    public function __isset($name): bool
    {
        return match($name) {
            'start_time', 'end_time', 'min_participants', 'max_participants',
            'current_participants', 'organizer_id', 'organizer_name',
            'is_free', 'created_at' => true,
            default => false
        };
    }

}
