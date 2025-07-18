<?php

namespace App\Application\Auth\Services;

use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;

class AuthService
{
    public function __construct(
        private readonly IUserRepository $repository
    )
    {
    }

    public function getUserByEmail(UserEmail $email)
    {
        return $this->repository->findByEmail($email);
    }

    public function authenticate(UserEmail $email, Password $password): ?User
    {
        $user = $this->repository->findByEmail($email);

        if (!$user || !$user->verifyPassword($password->plainValue())) {
            return null;
        }

        return $user;
    }
}
