<?php

namespace App\Application\Auth\Services;

use App\Application\RepositoryInterfaces\IUserRepository;
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
}
