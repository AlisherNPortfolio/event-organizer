<?php

namespace App\Presentation\Controllers\Dashboard;

use App\Application\Event\Services\EventService;
use App\Application\Event\Services\ParticipantService;
use App\Presentation\Controllers\Controller;
use App\Presentation\ViewModels\DashboardViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly ParticipantService $participantService
    )
    {}

    public function __invoke(Request $request): View
    {
        $user = Auth::user();
        $userEvents = $this->eventService->getUserEvents($user->id);
        $participants = $this->participantService->getUserParticipants($user->id);
        $atttandanceHistory = $this->participantService->getUserAttandanceHistory($user->id);
        $upcomingEvents = array_slice($this->eventService->getUpcomingEvents(), 0, 5);

        $viewModel = new DashboardViewModel(
            $user,
            $userEvents,
            $participants,
            $atttandanceHistory,
            $upcomingEvents
        );
        return view('dashboard.index', compact('viewModel'));
    }
}
