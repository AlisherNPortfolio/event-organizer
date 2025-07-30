<?php

namespace App\Providers;

use App\Application\RepositoryInterfaces\IEventRepository;
use App\Application\RepositoryInterfaces\IParticipantRepository;
use App\Application\RepositoryInterfaces\IUserRepository;
use App\Infrastructure\Repositories\EventRepository;
use App\Infrastructure\Repositories\ParticipantRepository;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IEventRepository::class, EventRepository::class);
        $this->app->bind(IParticipantRepository::class, ParticipantRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
