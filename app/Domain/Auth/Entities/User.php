<?php

namespace App\Domain\Auth\Entities;

use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Shared\Entity\BaseEntity;
use DateTime;

class User extends BaseEntity
{
    private int $rating;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    private array $events = [];

    public function __construct(
        private UserId $id,
        private string $name,
        private UserEmail $email,
        private Password $password,
        private ?string $avatar = null,
    )
    {
        $this->rating = 0;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public static function fromDatabase(
        UserId $userId,
        string $name,
        UserEmail $email,
        Password $password,
        int $rating,
        DateTime $createdAt,
        DateTime $updatedAt,
        ?string $avatar = null
    ): self
    {
        $user = new self(
            $userId,
            $name,
            $email,
            $password,
            $avatar
        );

        $user->rating = $rating;
        $user->createdAt = $createdAt;
        $user->updatedAt = $updatedAt;

        return $user;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return $this->password->verify($plainPassword);
    }

    public function updatePassword(Password $password): void
    {
        if (!$this->password->equals($password)) {
            $this->password = $password;
            $this->updatedAt = new DateTime();
        }
    }

    public function decreaseRating(): void
    {
        $oldRating = $this->rating;
        $this->rating = max(0, $oldRating - 1);
        $this->updatedAt = new DateTime();
    }

    public function increaseRating(): void
    {
        // $oldRating = $this->rating;
        $this->rating++;
        $this->updatedAt = new DateTime();
    }
}
