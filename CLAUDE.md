# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

"Tadbirlar yaratish uchun website" — a Laravel 12 event-organizer app (create events, join as a participant, upload event photos, mark attendance). UI strings, validation messages, and exceptions are written in Uzbek. The backend is built with **DDD and CQRS**; the frontend is Blade + Vue 3 islands mounted into specific pages (not an SPA).

## Common commands

Run via Docker Compose (services: `server` (nginx), `php`, `mysql_db`, `composer`, `artisan`, `npm`, `phpmyadmin`):

```bash
docker compose up -d                         # start the stack
docker compose run --rm composer install     # install PHP deps
docker compose run --rm npm install          # install JS deps
docker compose run --rm artisan migrate      # run migrations
docker compose run --rm artisan <command>    # any artisan command
```

If running PHP/Node locally instead of through Docker:

```bash
composer install
npm install
php artisan migrate
composer test                    # clears config cache, then runs php artisan test
php artisan test                 # run the full test suite
php artisan test --filter=Name   # run a single test (method or class name)
php artisan test tests/Feature/ExampleTest.php   # run a single test file
vendor/bin/pint                  # Laravel Pint code formatting
npm run dev                      # Vite dev server
npm run build                    # Vite production build
composer dev                     # runs serve + queue:listen + pail + vite concurrently
```

Tests use Laravel's default PHPUnit setup (`phpunit.xml`, `tests/Feature`, `tests/Unit`); there is no Pest install despite `pestphp/pest-plugin` being allowlisted in composer config.

## Architecture: DDD + CQRS layering

Code under `app/` is organized by architectural layer first, then by feature/bounded-context second (`Auth`, `Event`, `Profile`). When adding a feature, touch all four layers rather than putting logic in a controller or model.

```
app/Domain/<Context>/          Entities, ValueObjects, Enums, Factories — plain PHP, no Eloquent/Laravel deps
app/Application/<Context>/     Commands, Queries, CommandHandlers, QueryHandlers, DTOs, Services
app/Application/RepositoryInterfaces/   Interfaces the Application layer depends on
app/Infrastructure/            Eloquent Models, Repository implementations, artisan Commands, Notifications
app/Presentation/              Controllers, FormRequests, ViewModels, Middleware
```

**Flow for a write operation:** Controller builds a `Command` (`app/Application/<Context>/Commands`) from a validated `FormRequest` → the matching `CommandHandler` (extends `App\Application\Bus\CommandHandler`) is invoked (currently controllers call handlers directly via constructor injection — see below) → the handler loads/mutates a `Domain` entity (e.g. `App\Domain\Event\Entities\Event`) → persists it via a repository interface (`IEventRepository` etc.) whose Eloquent-backed implementation lives in `app/Infrastructure/Repositories`.

**Flow for a read operation:** same shape but with `Query` / `QueryHandler`, returning DTOs (`app/Application/<Context>/DTO`) that Presentation wraps in a `ViewModel` (`app/Presentation/ViewModels`) before handing to a Blade view.

**Domain entities are framework-free.** `App\Domain\Event\Entities\Event` has no Eloquent inheritance; it enforces invariants itself (e.g. `join()`, `markAsOngoing()`, `addPhoto()` throw `InvalidArgumentException` — with Uzbek messages — on invalid transitions) and is constructed via named factory methods (`create()` for new entities, `fromDatabase()` for hydration). Value objects (`EventId`, `EventTitle`, `UserId`, `ParticipantLimit`, `EventPrice`, ...) live in `app/Domain/<Context>/ValueObjects` and wrap primitives with validation.

**Repositories translate between layers.** `app/Infrastructure/Repositories/*Repository.php` implement the `Application/RepositoryInterfaces` contracts, converting Eloquent models (`app/Infrastructure/Models`) to/from Domain entities via a private `toDomainEvent()`-style mapper. Bindings for interface → implementation are registered in `app/Providers/RepositoryServiceProvider.php`.

**Command/Query buses wrap Laravel's `Illuminate\Bus\Dispatcher`** (`app/Application/Bus/IlluminateCommandBus.php`, `IlluminateQueryBus.php`), bound as singletons and populated with a `Command::class => Handler::class` map in `app/Providers/AppServiceProvider.php::boot()`. New commands/queries must be added to those maps to be dispatchable through the bus — note that several controllers currently bypass the bus and call handler classes directly via constructor injection instead (e.g. `EventController`), so check the existing pattern in the controller you're editing before introducing a new one.

**Scheduled event status transitions:** `app/Infrastructure/Commands/ProcessEventStatus.php` (signature `events:process-status`) promotes `upcoming` → `ongoing` → `completed` via `EventService`. It's registered in `bootstrap/app.php` via `->withSchedule()` — note the schedule currently references `event:process-status` (singular), which does not match the command's actual signature `events:process-status` (plural); verify this before assuming the scheduled job runs. The container's cron entrypoint is `scripts/run-scheduler-cron.sh`, wired via `_container_data/cron/Crontab`.

## Frontend

Vue 3 components are mounted as named components (not routed) into a global Vue app in `resources/js/app.js` (`app.component('main-event-view-component', ...)` etc.), then referenced by tag name from Blade templates in `resources/views`. Feature-specific Vue code lives under `resources/js/components/<feature>/` with shared logic in `resources/js/composables/<feature>/`. Styling is Tailwind v4 via `@tailwindcss/vite`; build tool is Vite (`vite.config.js`).

## Notes

- `app/Utils/helpers.php` is autoloaded globally via Composer's `files` autoload (not PSR-4). It defines `is_development()` and `get_exception_message($message, $exceptionMessage)`, which controllers use to conditionally append raw exception text to user-facing (Uzbek) error messages only outside production.
- `config/settings.php` and `config/captcha.php` are app-specific config beyond Laravel defaults (captcha via `mews/captcha`, aliased as the `Captcha` facade in `AppServiceProvider`).
- Auth currently binds no custom middleware alias (`AuthenticatedMiddleware` exists at `app/Presentation/Middleware/AuthenticatedMiddleware.php` but is commented out in `bootstrap/app.php`); routes use Laravel's built-in `auth`/`guest`/`verified` middleware.
