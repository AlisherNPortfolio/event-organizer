<?php

namespace App\Presentation\ViewModels;

class EventListViewModel
{
    public function __construct(
        private array $events,
        private array $filters = []
    ) {}

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function hasEvents(): bool
    {
        return count($this->events) > 0;
    }

    public function getEventsCount(): int
    {
        return count($this->events);
    }

    public function getSearchQuery(): ?string
    {
        return $this->filters['search'] ?? null;
    }

    public function isFiltered(): bool
    {
        return !empty(array_filter($this->filters));
    }
}
