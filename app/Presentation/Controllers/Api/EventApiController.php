<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Event\Queries\GetSimilarEventsQuery;
use App\Application\Event\QueryHandlers\GetSimilarEventsQueryHandler;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    public function __construct(
        private readonly GetSimilarEventsQueryHandler $getSimilarEventsQueryHandler
    )
    {}

    public function similarEvents(Request $request, string $eventId): JsonResponse
    {
        try {
            $query = new GetSimilarEventsQuery(
                eventId: new EventId($eventId),
                page: $request->get('page', 1),
                perPage: 3,
                search: $request->get('search'),
                filters: $request->only(['status', 'is_free', 'start_date', 'end_date'])
            );

            $events = $this->getSimilarEventsQueryHandler->handle($query);
            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (Exception $e) {
            $message = get_exception_message("O'xshash tadbirlarni olishda xatolik.", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }
    }
}
