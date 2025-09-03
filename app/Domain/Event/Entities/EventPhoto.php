<?php

namespace App\Domain\Event\Entities;

use App\Domain\Auth\ValueObjects\UserId;
use App\Domain\Event\ValueObjects\EventId;
use DateTime;
use Illuminate\Support\Str;

class EventPhoto
{
    private DateTime $uploadedAt;
    private string $id;

    private Participant $uploader;

    public function __construct(private EventId $eventId, private UserId $uploadedBy, private string $path)
    {
        $this->uploadedAt = new DateTime();
        $this->id = Str::uuid();
    }

    public static function fromDatabase(
        string $id,
        EventId $eventId,
        UserId $uploadedBy,
        DateTime $uploadedAt,
        string $path,
        ?Participant $participant = null
    ): self
    {
        $eventPhoto = new self(
            $eventId,
            $uploadedBy,
            $path
        );

        $eventPhoto->id = $id;
        $eventPhoto->uploadedAt = $uploadedAt;
        if ($participant) {
            $eventPhoto->uploader = $participant;
        }

        return $eventPhoto;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEventId(): EventId
    {
        return $this->eventId;
    }

    public function getUploadedBy(): UserId
    {
        return $this->uploadedBy;
    }

    public function getUploadedAt(): DateTime
    {
        return $this->uploadedAt;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUploader(): Participant
    {
        return $this->uploader;
    }
}
