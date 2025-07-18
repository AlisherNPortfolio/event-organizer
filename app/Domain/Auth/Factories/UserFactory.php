<?php

namespace App\Domain\Auth\Factories;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Domain\Auth\ValueObjects\UserId;

class UserFactory
{
    public static function create(string $name, string $email, Password $password): User
    {
        return new User(
            id: UserId::generate(),
            name: $name,
            email: UserEmail::from($email),
            password: $password
        );
    }
}
