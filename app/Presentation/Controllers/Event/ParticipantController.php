<?php

namespace App\Presentation\Controllers\Event;

use App\Application\Event\CommandHandlers\JoinEventCommandHandler;
use App\Application\Event\Commands\JoinEventCommand;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    public function __construct(
        private JoinEventCommandHandler $joinEventCommandHandler
    )
    {}

    public function join(Request $request, string $eventId)
    {
        try {
            $command = new JoinEventCommand(
                new EventId($eventId),
                new UserId(Auth::id())
            );

            $this->joinEventCommandHandler->handle($command);
            if ($request->wantsJson()) {
                return response()->json([
                    "message" => "Tadbirga muvaffaqiyatli qo'shildingiz"
                ]);
            }
            return back()->with("success", "Tadbirga muvaffaqiyatli qo'shildingiz");
        } catch (Exception $e) {
            $message = get_exception_message("Tadbirga qo'shilishda xatolik.", $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    "message" => $message
                ], 400);
            }
            return back()->with("error", $message);
        }
    }
}
