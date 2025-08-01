<?php

namespace App\Presentation\ViewModels;

use App\Application\Event\DTO\EventDTO;
use Illuminate\Support\Facades\Auth;

class EventViewModel
{
    public function __construct(
        private EventDTO $event,
        private bool $isParticipating = false,
        private array $participants = [],
        // private array $similarEvents = [],
        private array $stats = []
    ) {}

    public function getEvent(): EventDTO
    {
        return $this->event;
    }

    public function isParticipating(): bool
    {
        return $this->isParticipating;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    // public function getSimilarEvents(): array
    // {
    //     return $this->similarEvents;
    // }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function getFormattedStartTime(): string
    {
        return $this->event->startTime->format('d.m.Y H:i');
    }

    public function getFormattedEndTime(): ?string
    {
        return $this->event->endTime?->format('d.m.Y H:i');
    }

    public function getParticipantProgress(): int
    {
        if ($this->event->maxParticipants === 0) {
            return 0;
        }
        return (int) (($this->event->currentParticipants / $this->event->maxParticipants) * 100);
    }

    public function isFull(): bool
    {
        return $this->event->currentParticipants >= $this->event->maxParticipants;
    }

    public function canJoin(): bool
    {
        // return $this->event->status === 'upcoming' && !$this->isFull();
        return $this->event->status === 'upcoming' &&
               !$this->isFull() &&
               !$this->isParticipating() &&
               $this->event->organizerId !== Auth::id();
    }

    public function canLeave(): bool
    {
        return $this->event->status === 'upcoming' &&
               $this->isParticipating() &&
               $this->event->organizerId !== Auth::id();
    }

    public function canEdit(): bool
    {
        return $this->event->organizerId === Auth::id();
    }

    public function canMarkAttendance(): bool
    {
        return $this->event->organizerId === Auth::id() &&
               $this->event->status === 'completed';
    }

    public function canUploadPhoto(): bool
    {
        return $this->event->status === 'ongoing' &&
               ($this->isParticipating() || $this->canEdit());
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->event->status) {
            'upcoming' => 'bg-blue-100 text-blue-800',
            'ongoing' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusText(): string
    {
        return match($this->event->status) {
            'upcoming' => 'Kutilmoqda',
            'ongoing' => 'Davom etmoqda',
            'completed' => 'Tugallangan',
            default => 'Noma\'lum'
        };
    }

    public function getAttendanceRate(): float
    {
        if (empty($this->stats['total_participants'])) {
            return 0;
        }

        $attendedCount = $this->stats['attended_count'] ?? 0;
        $totalParticipants = $this->stats['total_participants'];

        return ($attendedCount / $totalParticipants) * 100;
    }

    public function getParticipantCount(): int
    {
        return count($this->participants);
    }

    public function getMarkedParticipants(): int
    {
        return $this->stats['marked_count'] ?? 0;
    }

    public function getPendingMarkings(): int
    {
        return $this->stats['pending_marking'] ?? 0;
    }

    public function hasUnmarkedParticipants(): bool
    {
        return $this->getPendingMarkings() > 0;
    }

    public function isEventOwner(): bool
    {
        return $this->event->organizerId === Auth::id();
    }

    public function getDurationHours(): ?int
    {
        if (!$this->event->endTime) {
            return null;
        }

        $duration = $this->event->startTime->diff($this->event->endTime);
        return $duration->h + ($duration->days * 24);
    }

    public function getDaysUntilEvent(): int
    {
        $now = new \DateTime();
        $diff = $now->diff($this->event->startTime);

        if ($this->event->startTime < $now) {
            return 0;
        }

        return $diff->days;
    }

    public function hasStarted(): bool
    {
        return new \DateTime() >= $this->event->startTime;
    }

    public function hasEnded(): bool
    {
        if (!$this->event->endTime) {
            return false;
        }

        return new \DateTime() > $this->event->endTime;
    }

    public function getTimeStatus(): string
    {
        $now = new \DateTime();

        if ($now < $this->event->startTime) {
            return 'upcoming';
        }

        if ($this->event->endTime && $now > $this->event->endTime) {
            return 'ended';
        }

        return 'ongoing';
    }

    public function getTimeUntilStart(): string
    {
        $now = new \DateTime();

        if ($now >= $this->event->startTime) {
            return 'Boshlangan';
        }

        $diff = $now->diff($this->event->startTime);

        if ($diff->days > 0) {
            return $diff->days . ' kun';
        }

        if ($diff->h > 0) {
            return $diff->h . ' soat';
        }

        return $diff->i . ' daqiqa';
    }
}
