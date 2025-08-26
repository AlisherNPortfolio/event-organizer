<?php

namespace App\Presentation\Controllers\Event;

use App\Application\Event\CommandHandlers\CreateEventCommandHandler;
use App\Application\Event\CommandHandlers\EditEventCommandHandler;
use App\Application\Event\Commands\CreateEventCommand;
use App\Application\Event\Commands\EditEventCommand;
use App\Application\Event\Queries\GetEventQuery;
use App\Application\Event\Queries\GetEventsQuery;
use App\Application\Event\QueryHandlers\GetEventQueryHandler;
use App\Application\Event\QueryHandlers\GetEventsQueryHandler;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventTitle;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Event\CreateEventRequest;
use App\Presentation\Requests\Event\UpdateEventRequest;
use App\Presentation\ViewModels\EventListViewModel;
use App\Presentation\ViewModels\EventViewModel;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    public function __construct(
        private GetEventsQueryHandler $getEventsQueryHandler,
        private GetEventQueryHandler $getEventQueryHandler,
        private CreateEventCommandHandler $createEventCommandHandler,
        private EditEventCommandHandler $editEventCommandHandler
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

    public function show(string $uuid): View|RedirectResponse
    {
        try {
            $query = new GetEventQuery(
                new EventId($uuid)
            );

            [
                $eventDTO,
                $isParticipating,
                $participants,
                $statistics
            ] = $this->getEventQueryHandler->handle($query);

            $viewModel = new EventViewModel($eventDTO, $isParticipating, $participants, $statistics);

            return view('events.show', compact('viewModel'));
        } catch (Exception $e) {
            $message = get_exception_message("Tadbirni olishda muammo. ", $e->getMessage());
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(CreateEventRequest $request): RedirectResponse
    {
        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            $incomingData = $request->validated();

            $command = new CreateEventCommand(
                new UserId(Auth::id()),
                new EventTitle($incomingData['title']),
                new EventDescription($incomingData['description']),
                $incomingData['address'],
                $incomingData['min_participants'],
                $incomingData['max_participants'],
                $incomingData['price'] ?? 0,
                $incomingData['currency'] ?? 'UZS',
                $imagePath,
                $incomingData['start_time'],
                $incomingData['end_time']
            );

            $eventId = $this->createEventCommandHandler->handle($command);

            return redirect()->route('events.show', $eventId)
                            ->with('success', 'Tadbir yaratildi');
        } catch (Exception $e) {
            if (isset($images)) {
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $message = get_exception_message(
                'Tadbirni saqlashda xatolik yuz berdi. Xato: ',
                $e->getMessage()
            );
            return redirect()->back()->withErrors(['error' => $message])->withInput();
        }
    }

    public function edit(string $uuid): View|RedirectResponse
    {
        try {
            $query = new GetEventQuery(
                new EventId($uuid)
            );

            [$eventDTO] = $this->getEventQueryHandler->handle($query, true);

            abort_if(
                !$eventDTO,
                Response::HTTP_NOT_FOUND,
                "Event topilmadi"
            );

            abort_if(
                $eventDTO->organizerId !== Auth::id(),
                Response::HTTP_FORBIDDEN,
                "Sizda bu tadbirni tahrirlash huquqi yo'q"
            );

            if ($eventDTO->status !== 'upcoming') {
                return redirect()->route('events.show', $uuid)
                    ->withErrors(['error' => "Faqat bo'lishi kutilayotgan tadbirlarni tahrirlash mumkin."]);
            }

            $viewModel = new EventViewModel($eventDTO);

            return view('events.edit', compact('viewModel'));

        } catch (Exception $e) {
            $message = get_exception_message("Tadbirni o'zgartirishda muammo.", $e->getMessage());
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }

    public function update(UpdateEventRequest $request, string $uuid)
    {
        try {
            $eventId = new EventId($uuid);
            $request->validated();

            $command = new EditEventCommand(
                $eventId,
                new EventTitle($request->title),
                new EventDescription($request->description),
                $request->address,
                $request->start_time,
                $request->min_participants,
                $request->has('max_participants') ? $request->max_participants : null,
                $request->has('price') ? $request->price : null,
                $request->has('currency') ? $request->currency : null,
                $request->has('end_time') ? $request->end_time : null,
                $request->hasFile('image') ? $request->file('image') : null
            );

            $this->editEventCommandHandler->handle($command);

            return redirect()->route('events.show', $uuid)
                ->with('success', 'Tadbir muvaffaqiyatli yangilandi');
        } catch (Exception $e) {
            $message = get_exception_message(
                "Tadbirni yangilashda xatolik.",
                $e->getMessage()
            );
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }
}
