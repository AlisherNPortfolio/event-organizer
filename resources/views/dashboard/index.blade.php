@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<dashboard-component
    :user='@json($viewModel->getUser())'
    :user-rating='@json($viewModel->getUserRating())'
    :user-events='@json($viewModel->getUserEvents())'
    :upcoming-events-count='@json($viewModel->getUpcomingEventsCount())'
    :completed-events-count='@json($viewModel->getCompletedEventsCount())'
    :total-participants='@json($viewModel->getTotalParticipantsCount())'
></dashboard-component>
@endsection
@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.bg-gradient-to-r {
    background: linear-gradient(to right, var(--tw-gradient-stops));
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.flow-root::-webkit-scrollbar {
    width: 4px;
}

.flow-root::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.flow-root::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.flow-root::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.text-blue-0f55ae {
    color: #0f55ae;
}

.text-blue-6b667d {
    color: #6b667d;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endpush
