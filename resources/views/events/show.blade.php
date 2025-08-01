@extends('layouts.main')

@section('title', $viewModel->getEvent()->title)

@section('content')
@php
    $isParticipating = false;
    if (auth()->check()) {
        $isParticipating = \App\Infrastructure\Models\Participant::where([
            'event_id' => $viewModel->getEvent()->id,
            'user_id' => auth()->id()
        ])->exists();
    }

    $canJoinEvent = $viewModel->canJoin();
    $isEventFull = $viewModel->isFull();
@endphp
<div id="event-show-app">
    <main-event-view-component
        :event="{{ json_encode($viewModel->getEvent()->toArray()) }}"
        :current-user="{{ json_encode(auth()->user()) }}"
        :is-participating="{{ json_encode($isParticipating ?? false) }}"
        :can-join="{{ json_encode($canJoinEvent) }}"
        :is-full="{{ json_encode($isEventFull) }}"
        csrf-token="{{ csrf_token() }}"
        @auth
        temp-bearer-token="{{ 'Bearer ' . auth()->user()->createToken('temp')->plainTextToken ?? '' }}"
        @endauth
        @if(session('error'))
        :backend-error={{ session('error') }}
        @endif
    />
</div>

@endsection
