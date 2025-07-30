<?php

namespace App\Presentation\Controllers\Event;

use App\Application\Event\Queries\GetEventsQuery;
use App\Application\Event\QueryHandlers\GetEventsQueryHandler;
use App\Presentation\Controllers\Controller;
use App\Presentation\ViewModels\EventListViewModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private GetEventsQueryHandler $getEventsQueryHandler
    )
    {}

    public function index(Request $request): View
    {
        try {
            $query = new GetEventsQuery(
                page: $request->get('page', 1),
                perPage: 12,
                filters: $request->only(['status', 'is_free', 'start_date', 'end_date']),
                search: $request->get('search')
            );

            $events = $this->getEventsQueryHandler->handle($query);
            $viewModel = new EventListViewModel($events, $request->all());

            return view('events.index', compact('viewModel'));
        } catch (Exception $e) {
            return view('events.index', [
                'viewModel' => new EventListViewModel([], []),
                'error' => "Tadbirlarni yuklashda xatolik yuz berdi"
            ]);
        }
    }
}
