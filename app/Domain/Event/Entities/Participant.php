<?php

namespace App\Domain\Event\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use DateTime;

class Participant
{
    private DateTime $joinedAt;
    private bool $attended = false;
    private bool $marked = false;
    private array $userData = [
        'id' => null,
        'name' => null,
        'email' => null,
        'avatar' => null,
        'rating' => null
    ];

    public function __construct(private EventId $eventId, private UserId $userId)
    {
        $this->joinedAt = new DateTime();
        $this->userData['id'] = $userId->value();
    }

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

    public function getUserData(): array
    {
        return $this->userData;
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

    public function setUserName(string $userName): void
    {
        $this->userData['name'] = $userName;
    }

    public function setUserEmail(string $userEmail): void
    {
        $this->userData['email'] = $userEmail;
    }

    public function setUserAvatar(?string $avatar): void
    {
        $this->userData['avatar'] = $avatar;
    }

    public function setUserRating(float $rating): void
    {
        $this->userData['rating'] = $rating;
    }
}
