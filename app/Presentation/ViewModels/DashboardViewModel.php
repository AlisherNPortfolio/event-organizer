<?php

namespace App\Presentation\ViewModels;

use App\Infrastructure\Models\User;

class DashboardViewModel
{
    public function __construct(
        private User $user,
        private array $userEvents,
        private array $participants,
        private array $attendanceHistory = [],
        private array $upcomingEvents = [],
    )
    {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserEvents(): array
    {
        return $this->userEvents;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function getAttendanceHistory(): array
    {
        return $this->attendanceHistory;
    }

    public function getUpcomingEvents(): array
    {
        return $this->upcomingEvents;
    }

    public function getUpcomingEventsCount(): int
    {
        return count(array_filter($this->userEvents, fn($event) => $event->status === 'upcoming'));
    }

    public function getCompletedEventsCount(): int
    {
        return count(array_filter($this->userEvents, fn($event) => $event->status === 'completed'));
    }

    public function getUserRating(): float
    {
        return $this->user->rating ?? 0.0;
    }

    public function getTotalParticipantsCount(): int
    {
        return array_sum(array_map(fn($event) => $event->currentParticipants ?? 0, $this->userEvents));
    }

    public function getParticipatedEventsCount(): int
    {
        return count($this->participants);
    }

    public function getAttendanceRate(): float
    {
        if (empty($this->attendanceHistory) || !isset($this->attendanceHistory['total_events'])) {
            return 0.0;
        }

        return $this->attendanceHistory['attendance_rate'] ?? 0.0;
    }

    public function getTotalEventsAttended(): int
    {
        return $this->attendanceHistory['attended_count'] ?? 0;
    }

    public function getTotalEventsMissed(): int
    {
        return $this->attendanceHistory['missed_events'] ?? 0;
    }

    public function hasEvents(): bool
    {
        return !empty($this->userEvents);
    }

    public function hasParticipants(): bool
    {
        return !empty($this->participants);
    }

    public function getRecentEvents(int $limit = 5): array
    {
        return array_slice($this->userEvents, 0, $limit);
    }
}
