<?php

namespace App\Application\RepositoryInterfaces;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Domain\Auth\ValueObjects\UserId;

interface IUserRepository
{
    public function save(User $user): void;
    public function findById(UserId $id): ?User;
    public function findByEmail(UserEmail $email): ?User;
    public function delete(UserId $id): void;
    public function findAll(int $limit): array;
}
