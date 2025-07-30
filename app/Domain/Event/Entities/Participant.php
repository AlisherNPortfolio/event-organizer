<?php

namespace App\Domain\Event\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use DateTime;

class Participant
{
    private DateTime $joinedAt = new DateTime();
    private bool $attended = false;
    private bool $marked = false;

    public function __construct(private EventId $eventId, private UserId $userId)
    {}

    public function isAttended(): bool
    {
        return $this->attended;
    }

    public function isMarked(): bool
    {
        return $this->marked;
    }

    public function getEventId(): EventId
    {
        return $this->eventId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getJoinedAt(): DateTime
    {
        return $this->joinedAt;
    }

    public function markAsAttended(): void
    {
        $this->attended = true;
        $this->marked = true;
    }

    public function markAsNotAttended(): void
    {
        $this->attended = false;
        $this->marked = true;
    }
}
