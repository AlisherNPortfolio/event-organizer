<?php

namespace App\Application\Profile\Services;

use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\ValueObjects\UserId;

class ProfileService
{
    public function __construct(
        private readonly IUserRepository $userRepository
    )
    {}

    public function getUserProfile(UserId $userId)
    {

    }
}
