<?php

namespace App\Presentation\Controllers\Event;

use App\Application\Event\CommandHandlers\JoinEventCommandHandler;
use App\Application\Event\Commands\JoinEventCommand;
use App\Application\Event\Queries\GetUserParticipantsQuery;
use App\Application\Event\QueryHandlers\GetUserParticipantsQueryHandler;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function __construct(
        private JoinEventCommandHandler $joinEventCommandHandler,
        private readonly GetUserParticipantsQueryHandler $getUserParticipantsQueryHandler
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

    public function myParticipants(): View|RedirectResponse
    {
        try {
            $query = new GetUserParticipantsQuery(
                new UserId(Auth::id())
            );

            $participants = $this->getUserParticipantsQueryHandler->handle($query);

            return view("profile.my-participants", compact("participants"));
        } catch (Exception $e) {
            $message = get_exception_message("Xatolik yuz berdi.", $e->getMessage());
            return back()->with("error", $message);
        }
    }
}
