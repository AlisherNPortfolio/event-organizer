<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Event\CommandHandlers\LeaveEventCommandHandler;
use App\Application\Event\Commands\LeaveEventCommand;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;

class ParticipantApiController extends Controller
{
    public function __construct(
        private readonly LeaveEventCommandHandler $leaveEventCommandHandler
    )
    {}

    public function leave(string $eventId)
    {
        try {
            $command = new LeaveEventCommand(
                eventId: new EventId($eventId)
            );

            $hasLeft = $this->leaveEventCommandHandler->handle($command);
            if ($hasLeft) {
                return response()->json([
                    'message' => "Tadbirdan muvaffaqiyatli chiqib ketdingiz"
                ], 200);
            } else {
                return response()->json([
                    'message' => "Tadbirdan chiqib ketishda xatolik yuz berdi"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
