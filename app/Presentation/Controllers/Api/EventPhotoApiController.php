<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Event\CommandHandlers\UploadEventPhotoCommandHandler;
use App\Application\Event\Commands\UploadEventPhotoCommand;
use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Event\UploadPhotoRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventPhotoApiController extends Controller
{
    public function __construct(
        private readonly UploadEventPhotoCommandHandler $uploadEventPhotoCommandHandler
    )
    {}

    public function store(UploadPhotoRequest $request, string $eventId): JsonResponse
    {
        try {
            $photoPath = $request->file('photo')->store('event-photos', 'public');

            $command = new UploadEventPhotoCommand(
                new EventId($eventId),
                new UserId(Auth::id()),
                $photoPath
            );

            $eventPhoto = $this->uploadEventPhotoCommandHandler->handle($command);

            return response()->json([
                'success' => true,
                'message' => 'Rasm yuklandi',
                'photo' => [
                    'id' => $eventPhoto->getId(),
                    'path' => $eventPhoto->getPath(),
                    'full_url' => asset('storage' . $eventPhoto->getPath()),
                    'uploaded_at' => $eventPhoto->getUploadedAt()->format('Y-m-d H:i:s'),
                ]
            ], 201);
        } catch (Exception $e) {
            Log::error('Rasmni yuklashda xatolik. Error: ' . $e->getMessage());

            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $message = get_exception_message("Rasm yuklashda xatolik!", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }
    }
}
