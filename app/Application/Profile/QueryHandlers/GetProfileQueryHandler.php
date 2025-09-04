<?php

namespace App\Application\Profile\QueryHandlers;

use App\Application\Profile\Query\GetProfileQuery;
use App\Application\RepositoryInterfaces\IUserRepository;
use InvalidArgumentException;

class GetProfileQueryHandler
{
    public function __construct(
        private readonly IUserRepository $userRepository
    )
    {}

    public function handle(GetProfileQuery $query): array
    {
        $user = $this->userRepository->findById($query->userId);

        throw_if(
            !$user,
            new InvalidArgumentException("Foydalanuvchi topilmadi")
        );

        return [
            'id' => $user->getId()->value(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'rating' => $user->getRating(),
            'created_at' => $user->getCreatedAt()
        ];
    }
}
