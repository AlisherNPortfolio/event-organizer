<?php

namespace App\Providers;

use App\Application\Auth\CommandHandlers\LoginUserCommandHandler;
use App\Application\Auth\CommandHandlers\RegisterUserCommandHandler;
use App\Application\Auth\Commands\LoginUserCommand;
use App\Application\Auth\Commands\RegisterUserCommand;
use App\Application\Bus\IlluminateCommandBus;
use App\Application\Bus\IlluminateQueryBus;
use App\Application\Bus\IQueryBus;
use App\Application\Bus\ICommandBus;
use App\Application\Event\CommandHandlers\JoinEventCommandHandler;
use App\Application\Event\CommandHandlers\LeaveEventCommandHandler;
use App\Application\Event\CommandHandlers\MarkAttendanceCommandHandler;
use App\Application\Event\CommandHandlers\UploadEventPhotoCommandHandler;
use App\Application\Event\Commands\JoinEventCommand;
use App\Application\Event\Commands\LeaveEventCommand;
use App\Application\Event\Commands\MarkAttendanceCommand;
use App\Application\Event\Commands\UploadEventPhotoCommand;
use App\Application\Event\Queries\GetEventParticipantsQuery;
use App\Application\Event\Queries\GetEventPhotosQuery;
use App\Application\Event\Queries\GetEventQuery;
use App\Application\Event\Queries\GetEventsQuery;
use App\Application\Event\Queries\GetSimilarEventsQuery;
use App\Application\Event\QueryHandlers\GetEventParticipantsQueryHandler;
use App\Application\Event\QueryHandlers\GetEventPhotosQueryHandler;
use App\Application\Event\QueryHandlers\GetEventQueryHandler;
use App\Application\Event\QueryHandlers\GetEventsQueryHandler;
use App\Application\Event\QueryHandlers\GetSimilarEventsQueryHandler;
use App\Application\Profile\Query\GetProfileQuery;
use App\Application\Profile\QueryHandlers\GetProfileQueryHandler;
use Illuminate\Support\ServiceProvider;
use Mews\Captcha\Facades\Captcha;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('Captcha', Captcha::class);

        $this->app->singleton(
            ICommandBus::class,
            IlluminateCommandBus::class
        );

        $this->app->singleton(
            IQueryBus::class,
            IlluminateQueryBus::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $commandBus = app(ICommandBus::class);
        $commandBus->register([
            // command va command handler-lar bog'lanadi
            RegisterUserCommand::class => RegisterUserCommandHandler::class,
            LoginUserCommand::class => LoginUserCommandHandler::class,
            JoinEventCommand::class => JoinEventCommandHandler::class,
            LeaveEventCommand::class => LeaveEventCommandHandler::class,
            UploadEventPhotoCommand::class => UploadEventPhotoCommandHandler::class,
            MarkAttendanceCommand::class => MarkAttendanceCommandHandler::class
        ]);

        $queryBus = app(IQueryBus::class);
        $queryBus->register([
            // query va query handler-lar bog'lanadi
            GetEventsQuery::class => GetEventsQueryHandler::class,
            GetEventQuery::class => GetEventQueryHandler::class,
            GetEventPhotosQuery::class => GetEventPhotosQueryHandler::class,
            GetEventParticipantsQuery::class => GetEventParticipantsQueryHandler::class,
            GetSimilarEventsQuery::class => GetSimilarEventsQueryHandler::class,
            GetProfileQuery::class => GetProfileQueryHandler::class
        ]);
    }
}
