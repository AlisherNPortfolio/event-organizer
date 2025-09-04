<?php

namespace App\Presentation\ViewModels;

class ProfileViewModel
{
    private array $statistics;
    private array $recentAtivity;

    public function __construct(
        public array $profile,
        array $statistics
    )
    {
        $this->statistics = [
            'organized_events_total' => $statistics['organized_events_total'],
            'organized_events_upcoming' => $statistics['organized_events_upcoming'],
            'organized_events_completed' => $statistics['organized_events_completed'],
            'participated_events_total' => $statistics['participated_events_total'],
            'participant_events_attended' => $statistics['participant_events_attended'],
            'total_participants_attracted' => $statistics['total_participants_attracted'],
            'attendance_rate' => $statistics['attendance_rate'],
            'organized_events' => $statistics['organized_events'],
            'participants' => $statistics['participants']
        ];

        $this->recentAtivity = [
            'recent_organized' => $statistics['recent_organized'],
            'recent_participants' => $statistics['recent_participants']
        ];
    }

    public function getProfile(): array
    {
        return $this->profile;
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

    public function getRecentActivity(): array
    {
        return $this->recentAtivity;
    }

    public function getUserName(): string
    {
        return $this->profile['name'] ?? "Ism berilmagan";
    }

    public function getEmail(): string
    {
        return $this->profile['email'] ?? "Pochta manzili berilmagan";
    }

    public function getUserRating(): int
    {
        return $this->profile['rating'] ?? 0;
    }

    public function getMemberSince(): string
    {
        if (isset($this->profile['created_at'])) {
            return $this->profile['created_at']->format('F Y');
        }
        return "Noma'lum";
    }

    public function getOrganizedEventsCount(): int
    {
        return $this->statistics['organized_events_total'] ?? 0;
    }

    public function getParticipatedEventsTotal(): int
    {
        return $this->statistics['participant_events_total'] ?? 0;
    }

    public function getAttendanceRate(): float
    {
        return round($this->statistics['attendance_rate'] ?? 0, 1);
    }

    public function getTotalParticipantsAttracted(): int
    {
        return $this->statistics['total_participants_attracted'] ?? 0;
    }

    public function hasRecentAtivity(): bool
    {
        return !empty($this->recentAtivity);
    }

    public function getOrganizedEvents()
    {
        return $this->statistics['organized_events'];
    }

    public function getParticipants()
    {
        return $this->statistics['participants'];
    }
}
