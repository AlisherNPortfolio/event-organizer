@extends('layouts.app')

@section('title', 'Qatnashgan tadbirlarim')
@php
    $user = auth()->user();
@endphp
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Qatnashgan tadbirlarim</h1>
            <p class="mt-1 text-sm text-gray-500">Siz qatnashgan barcha tadbirlar</p>
        </div>
        <a href="{{ route('events.index') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Yangi tadbirlar topish
        </a>
    </div>

    @if(count($participants) > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($participants as $participant)
                    @php
                        $event = $participant['event'];
                        $p = $participant['participant'];
                    @endphp
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        @if(!empty($event->getImages()))
                                            <img class="h-16 w-16 rounded-lg object-cover"
                                                 src="{{ asset('storage/' . $event->getImages()[0]) }}"
                                                 alt="{{ $event->getTitle()->value() }}">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-300 flex items-center justify-center">
                                                <span class="text-lg font-medium text-gray-700">{{ substr($event->getTitle()->value(), 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-lg font-medium text-blue-600 truncate">
                                                {{ $event->getTitle()->value() }}
                                            </p>
                                            <div class="ml-2 flex flex-shrink-0 space-x-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($event->getStatus() === 'upcoming') bg-blue-100 text-blue-800
                                                    @elseif($event->getStatus() === 'ongoing') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if($event->getStatus() === 'upcoming') Kutilmoqda
                                                    @elseif($event->getStatus() === 'ongoing') Davom etmoqda
                                                    @else Tugallangan @endif
                                                </span>

                                                @if($p->isMarked())
                                                    @if($p->isAttended())
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            Qatnashgan
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            Qatnashmagan
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Belgilanmagan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="sm:flex sm:justify-between">
                                                <div class="sm:flex">
                                                    <p class="flex items-center text-sm text-gray-500">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $event->getStartTime()->format('d.m.Y H:i') }}
                                                    </p>
                                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $event->getAddress() }}
                                                    </p>
                                                </div>
                                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                    <p>Qo'shilgan: {{ $p->getJoinedAt()->format('d.m.Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('events.show', $event->getId()->value()) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                        Ko'rish
                                    </a>

                                    @if($event->getStatus() === 'upcoming')
                                        <form method="POST" action="{{ route('events.leave', $event->getId()->value()) }}"
                                              onsubmit="return confirm('Tadbirdan chiqishni tasdiqlaysizmi?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">
                                                Chiqish
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Qatnashgan tadbirlar yo'q</h3>
            <p class="mt-1 text-sm text-gray-500">Hali hech qanday tadbirda qatnashmadingiz</p>
            <div class="mt-6">
                <a href="{{ route('events.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Tadbirlarni ko'rish
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
