<?php

namespace App\Application\Event\DTO;

use DateTime;

class ParticipantDTO
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $userId,
        public readonly DateTime $joinedAt,
        public readonly bool $attended,
        public readonly bool $marked
    )
    {}

    public function toArray(): array
    {
        return [
            'eevnt_id' => $this->eventId,
            'user_id' => $this->userId,
            'joined_at' => $this->joinedAt->format('Y-m-d H:i:s'),
            'attended' => $this->attended,
            'marked' => $this->marked
        ];
    }
}
