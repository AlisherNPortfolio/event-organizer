<?php

namespace App\Infrastructure\Commands;

use App\Application\Event\Services\EventService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessEventStatus extends Command
{
    public function __construct(
        private readonly EventService $eventService
    )
    {
        parent::__construct();
    }

    protected $signature = "events:process-status";

    protected $description = "Tadbir holatini sanasiga qarab o'zgartirish";

    public function handle(): int
    {
        $this->info("Tadbirlarning holati ko'rib chiqish va o'zgartirilish jarayonida...");

        DB::beginTransaction();
        try {
            // "upcoming" statusli boshlangan tadbirlarni "ongoing" sifatida belgilash
            $this->eventService->markCurrentUpcomingAsOngoing();

            // "onging" statusli tugagan tadbirlarni "completed" sifatida belgilash
            $this->eventService->markCurrentOngoingAsCompleted();

            DB::commit();

            $this->info("Tadbirlar holatlarini o'zgartirish yakunlandi!");
            return Command::SUCCESS;
        } catch (Exception $e) {
            DB::rollBack();

            $message = get_exception_message("Tadbir holatini o'zgartirishda xatolik. ", $e->getMessage());
            $this->error($message);
            Log::error($message);

            return Command::FAILURE;
        }
    }
}
