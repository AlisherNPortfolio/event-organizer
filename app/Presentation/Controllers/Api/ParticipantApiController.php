<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Event\CommandHandlers\LeaveEventCommandHandler;
use App\Application\Event\CommandHandlers\MarkAttendanceCommandHandler;
use App\Application\Event\Commands\LeaveEventCommand;
use App\Application\Event\Commands\MarkAttendanceCommand;
use App\Application\Event\Queries\GetEventParticipantsQuery;
use App\Application\Event\QueryHandlers\GetEventParticipantsQueryHandler;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Event\MarkAttendanceRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ParticipantApiController extends Controller
{
    public function __construct(
        private readonly LeaveEventCommandHandler $leaveEventCommandHandler,
        private readonly GetEventParticipantsQueryHandler $getEventParticipantsQueryHandler,
        private readonly MarkAttendanceCommandHandler $markAttendanceCommandHandler
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

    public function eventParticipants(string $eventId): JsonResponse
    {
        try {
            $query = new GetEventParticipantsQuery(
                new EventId($eventId)
            );

            $eventParticipants = $this->getEventParticipantsQueryHandler->handle($query);

            return response()->json($eventParticipants, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function markAttendace(MarkAttendanceRequest $request, string $eventId): JsonResponse
    {
        try {
            $request->validated();

            $command = new MarkAttendanceCommand(
                new EventId($eventId),
                new UserId(Auth::id()),
                new UserId($request->input('participant_id')),
                $request->input('attended')
            );

            $this->markAttendanceCommandHandler->handle($command);
            $message = $request->input('attended') ? 'Qatnashchi ishtirok etgan deb belgilandi' : 'Qatnashchi ishtirok etmagan deb belgilandi';
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            $message = get_exception_message("Qatnashchini yo'qlama qilishda xatolik. ", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }
    }
}
