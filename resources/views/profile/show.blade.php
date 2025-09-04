@extends('layouts.main')

@section('title', 'Profil')
@php
    $stats = $viewModel->getStatistics();
    $organizedEvents = $viewModel->getOrganizedEvents();
    $participants = $viewModel->getParticipants();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img class="h-20 w-20 rounded-full object-cover"
                             src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-2xl font-medium text-gray-700">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="mt-2 flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $user->rating }} ball
                        </span>
                        <span class="text-sm text-gray-500">
                            Ro'yxatdan o'tgan: {{ $user->created_at->format('d.m.Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('profile.edit') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Profilni tahrirlash
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Yaratgan tadbirlar</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['organized_events_total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Qatnashgan tadbirlar</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['participated_events_total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Kutilayotgan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['organized_events_upcoming'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tugallangan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['organized_events_completed'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Reyting</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->rating }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- My Events -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Mening tadbirlarim</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">So'ngi yaratilgan tadbirlar</p>
                </div>
                <a href="{{ route('profile.my-events') }}" class="text-blue-600 hover:text-blue-500 text-sm">
                    Barchasini ko'rish →
                </a>
            </div>

            @if(count($organizedEvents) > 0)
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach(array_slice($organizedEvents, 0, 3) as $event)
                        <li class="px-4 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if(!empty($event->images))
                                            <img class="h-8 w-8 rounded object-cover"
                                                 src="{{ asset('storage/' . $event->images[0]) }}"
                                                 alt="{{ $event->title }}">
                                        @else
                                            <div class="h-8 w-8 rounded bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">{{ substr($event->title, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $event->currentParticipants }}/{{ $event->maxParticipants }} ishtirokchi
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($event->status === 'upcoming') bg-blue-100 text-blue-800
                                        @elseif($event->status === 'ongoing') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($event->status === 'upcoming') Kutilmoqda
                                        @elseif($event->status === 'ongoing') Davom etmoqda
                                        @else Tugallangan @endif
                                    </span>
                                    <a href="{{ route('events.show', $event->id) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm">Ko'rish</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-8 text-center">
                    <p class="text-gray-500">Hali hech qanday tadbir yaratmading</p>
                    <a href="{{ route('events.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Birinchi tadbiringizni yarating
                    </a>
                </div>
            @endif
        </div>

        <!-- My Participations -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Qatnashgan tadbirlar</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">So'ngi qatnashgan tadbirlar</p>
                </div>
                <a href="{{ route('profile.my-participations') }}" class="text-blue-600 hover:text-blue-500 text-sm">
                    Barchasini ko'rish →
                </a>
            </div>

            @if(count($participants) > 0)
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach(array_slice($participants, 0, 3) as $index => $participation)
                        @php $event = $participation['event']; @endphp
                        <li class="px-4 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($event['image'])
                                            <img class="h-8 w-8 rounded object-cover"
                                                 src="{{ asset('storage/' . $event['image']) }}"
                                                 alt="{{ $event['title'] }}">
                                        @else
                                            <div class="h-8 w-8 rounded bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">{{ substr($event['title'], 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event['title'] }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($participation['participant']->marked)
                                                @if($participation['participant']->attended)
                                                    <span class="text-green-600">Qatnashgan</span>
                                                @else
                                                    <span class="text-red-600">Qatnashmagan</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">Davomat belgilanmagan</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($event['status'] === 'upcoming') bg-blue-100 text-blue-800
                                        @elseif($event['status'] === 'ongoing') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($event['status'] === 'upcoming') Kutilmoqda
                                        @elseif($event['status'] === 'ongoing') Davom etmoqda
                                        @else Tugallangan @endif
                                    </span>
                                    <a href="{{ route('events.show', $event['id']) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm">Ko'rish</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-8 text-center">
                    <p class="text-gray-500">Hali hech qanday tadbirda qatnashmadingiz</p>
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 mt-4">
                        Tadbirlarni ko'rish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
