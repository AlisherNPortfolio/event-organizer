@extends('layouts.main')

@section('title', 'Profilni tahrirlash')

@php
    $user = auth()->user();
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Profile Information -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Profil ma'lumotlari</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Shaxsiy ma'lumotlaringizni yangilang</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="border-t border-gray-200 px-4 py-5 sm:p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Ism *</label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name', $user->name) }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email', $user->email) }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Joriy parol</label>
                    <input type="password" name="current_password" id="current_password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Parolni o'zgartirish uchun joriy parolni kiriting</p>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Yangi parol</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Kamida 8 ta belgi bo'lishi kerak</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Parolni tasdiqlang</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                <a href="{{ route('profile.show') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Bekor qilish
                </a>
                <button type="submit"
                        class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Saqlash
                </button>
            </div>
        </form>
    </div>

    <!-- Avatar Upload -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Avatar rasmi</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Profil rasmingizni yangilang</p>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img class="h-20 w-20 rounded-full object-cover"
                             src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-2xl font-medium text-gray-700">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-4">
                    <form method="POST" action="{{ route('profile.upload-avatar') }}" enctype="multipart/form-data" class="flex items-center space-x-4">
                        @csrf
                        <input type="file" name="avatar" accept="image/*" required
                               class="avatar-input block text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none">
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                            Yuklash
                        </button>
                    </form>

                    @if($user->avatar)
                        <form method="POST" action="{{ route('profile.delete-avatar') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm"
                                    onclick="return confirm('Avatarni o\'chirishni tasdiqlaysizmi?')">
                                Avatarni o'chirish
                            </button>
                        </form>
                    @endif

                    <p class="text-xs text-gray-500">JPG, PNG, GIF formatida, 2MB gacha</p>
                </div>
            </div>

            @error('avatar')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
@endsection
