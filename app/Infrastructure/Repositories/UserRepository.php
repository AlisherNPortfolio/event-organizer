<?php

namespace App\Infrastructure\Repositories;

use App\Application\RepositoryInterfaces\IUserRepository;
use App\Domain\Auth\Entities\User as DomainUser;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Domain\Auth\ValueObjects\UserId;
use App\Infrastructure\Models\User as EloquentUser;

class UserRepository implements IUserRepository
{
    public function save(DomainUser $user): void
    {
        try {
            $eloquentUser = EloquentUser::query()->find($user->getId()->value());

            if (!$eloquentUser) {
                $eloquentUser = new EloquentUser();
                $eloquentUser->id = $user->getId()->value();
            }

            $eloquentUser->name = $user->getName();
            $eloquentUser->email = $user->getEmail()->value();
            $eloquentUser->password = $user->getPassword()->value();
            $eloquentUser->avatar = $user->getAvatar() ?? null;
            $eloquentUser->rating = $user->getRating();
            $eloquentUser->updated_at = $user->getUpdatedAt();

            $eloquentUser->save();
        } catch (\Exception $e) {
            $message = get_exception_message('Foydalanuvchini saqlashda xatolik.', $e->getMessage());
            throw new \RuntimeException($message);
        }
    }

    public function findById(UserId $id): ?DomainUser
    {
        $eloquentUser = EloquentUser::query()->find($id->value());

        if (!$eloquentUser) {
            return null;
        }

        return $this->toDomainUser($eloquentUser);
    }

    public function findByEmail(UserEmail $email): ?DomainUser
    {
        $eloquentUser = EloquentUser::query()->where('email', $email->value())->first();

        if (!$eloquentUser) {
            return null;
        }

        return $this->toDomainUser($eloquentUser);
    }

    public function delete(UserId $id): void
    {
        $eloquentUser = EloquentUser::query()->find($id->value());

        if ($eloquentUser) {
            $eloquentUser->delete();
        }
    }

    public function findAll(int $limit): array
    {
        $eloquentUsers = EloquentUser::query()->limit($limit)->get();

        return $eloquentUsers->map(function (EloquentUser $user) {
            return $this->toDomainUser($user);
        })->toArray();
    }

    private function toDomainUser(EloquentUser $eloquentUser): DomainUser
    {
        return new DomainUser(
            new UserId($eloquentUser->id),
            $eloquentUser->name,
            new UserEmail($eloquentUser->email),
            new Password($eloquentUser->password, true),
            $eloquentUser->avatar
        );
    }
}
