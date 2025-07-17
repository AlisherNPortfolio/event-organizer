<?php

namespace App\Domain\Auth\Entities;

use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Shared\Entity\BaseEntity;

class User extends BaseEntity
{
    private int $rating;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    private array $events = [];

    public function __construct(
        private readonly UserId $id,
        private readonly string $name,
        private readonly UserEmail $email,
        private readonly Password $password,
        private readonly ?string $avatar = null,
    )
    {
        $this->rating = 0;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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


}
