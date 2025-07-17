<?php

namespace App\Providers;

use App\Application\Bus\IlluminateCommandBus;
use App\Application\Bus\IlluminateQueryBus;
use App\Application\Bus\IQueryBus;
use App\Application\Bus\ICommandBus;
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
            // CommandClass::class => CommandHandlerClass::class,
        ]);

        $queryBus = app(IQueryBus::class);
        $queryBus->register([
            // query va query handler-lar bog'lanadi
            // QueryClass::class => QueryHandlerClass::class,
        ]);
    }
}
