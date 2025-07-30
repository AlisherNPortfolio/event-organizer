@extends('layouts.main')

@section('title', 'Tadbirlar')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Tadbirlar</h1>
        @auth
            <a href="{{ route('events.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Yangi tadbir yaratish
            </a>
        @endauth
    </div>

    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-lg shadow">
        <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Qidiruv</label>
                    <input type="text" name="search" value="{{ $viewModel->getSearchQuery() }}"
                           placeholder="Tadbir nomi yoki tavsifi..."
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Holati</label>
                    <select name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Barchasi</option>
                        <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Kutilmoqda</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Davom etmoqda</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Tugallangan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Narx</label>
                    <select name="is_free" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Barchasi</option>
                        <option value="1" {{ request('is_free') === '1' ? 'selected' : '' }}>Bepul</option>
                        <option value="0" {{ request('is_free') === '0' ? 'selected' : '' }}>Pulli</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Qidirish
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Events List -->
    @if($viewModel->hasEvents())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($viewModel->getEvents() as $event)
                @php
                    // Status badge class
                    $statusBadgeClass = match($event->status) {
                        'upcoming' => 'bg-blue-100 text-blue-800',
                        'ongoing' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-gray-100 text-gray-800',
                        default => 'bg-gray-100 text-gray-800'
                    };

                    // Status text
                    $statusText = match($event->status) {
                        'upcoming' => 'Kutilmoqda',
                        'ongoing' => 'Davom etmoqda',
                        'completed' => 'Tugallangan',
                        default => 'Noma\'lum'
                    };

                    // Progress calculation
                    $progress = $event->maxParticipants > 0 ? ($event->currentParticipants / $event->maxParticipants) * 100 : 0;

                    // Can join check
                    $canJoin = $event->status === 'upcoming' &&
                               $event->currentParticipants < $event->maxParticipants &&
                               (!auth()->check() || $event->organizerId !== auth()->id());
                @endphp

                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative">
                        @if(!empty($event->images))
                            <img src="{{ asset('storage/' . $event->images[0]) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Rasm yo'q</span>
                            </div>
                        @endif

                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            {{ Str::limit($event->description, 100) }}
                        </p>

                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $event->startTime->format('d.m.Y H:i') }}</span>
                            </div>

                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ Str::limit($event->address, 30) }}</span>
                            </div>

                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                <span>{{ $event->currentParticipants }}/{{ $event->maxParticipants }} ishtirokchi</span>
                            </div>

                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                                @if($event->isFree)
                                    <span class="text-green-600 font-medium">Bepul</span>
                                @else
                                    <span>{{ number_format($event->price, 0) }} {{ $event->currency }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ round($progress) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ round($progress) }}% to'ldi</p>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('events.show', $event->id) }}"
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                Ko'rish
                            </a>

                            @auth
                                @if($canJoin)
                                    <form method="POST" action="{{ route('events.join', $event->id) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                            Qatnashish
                                        </button>
                                    </form>
                                @elseif($event->status === 'upcoming' && $event->currentParticipants >= $event->maxParticipants)
                                    <button disabled class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed">
                                        To'ldi
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                    Qatnashish
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(count($viewModel->getEvents()) >= 10)
            <div class="flex justify-center mt-8">
                <nav class="flex space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                        <a href="{{ route('events.index', array_merge(request()->query(), ['page' => $i])) }}"
                           class="px-3 py-2 text-sm bg-white border border-gray-300 text-gray-500 hover:bg-gray-50
                                  {{ request('page', 1) == $i ? 'bg-blue-50 border-blue-500 text-blue-600' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </nav>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Hech qanday tadbir topilmadi</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($viewModel->isFiltered())
                        Filter parametrlarini o'zgartirib ko'ring
                    @else
                        Birinchi tadbir yaratuvchi bo'ling!
                    @endif
                </p>
                <div class="mt-6">
                    @if($viewModel->isFiltered())
                        <a href="{{ route('events.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Barcha tadbirlarni ko'rish
                        </a>
                    @else
                        @auth
                            <a href="{{ route('events.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Yangi tadbir yaratish
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Ro'yxatdan o'ting
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
