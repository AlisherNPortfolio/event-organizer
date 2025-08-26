@extends('layouts.main')

@section('title', 'Yangi tadbir yaratish')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Yangi tadbir yaratish</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Tadbir haqida to'liq ma'lumot kiriting</p>
        </div>

        <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Main image -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">Asosiy rasm *</label>
                    <input type="file" name="image" id="image" accept="image/*" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">JPEG, PNG, JPG, GIF formatida, 2MB dan kichik bo'lishi kerak</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Tadbir nomi *</label>
                    <input type="text" name="title" id="title" required maxlength="100"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('title') }}" placeholder="Masalan: Futbol o'yini">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Tadbir tavsifi *</label>
                    <textarea name="description" id="description" rows="4" required maxlength="2000"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Tadbir haqida batafsil ma'lumot...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Manzil *</label>
                    <input type="text" name="address" id="address" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('address') }}" placeholder="To'liq manzil">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Boshlanish vaqti *</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('start_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Tugash vaqti (ixtiyoriy)</label>
                        <input type="datetime-local" name="end_time" id="end_time"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('end_time') }}">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Participants -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_participants" class="block text-sm font-medium text-gray-700">Minimal qatnashchilar soni *</label>
                        <input type="number" name="min_participants" id="min_participants" required min="1"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('min_participants', 1) }}">
                        @error('min_participants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700">Maksimal qatnashchilar soni *</label>
                        <input type="number" name="max_participants" id="max_participants" required min="1"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('max_participants', 10) }}">
                        @error('max_participants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Price -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Narx (0 = bepul)</label>
                        <input type="number" name="price" id="price" min="0" step="1000"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('price', 0) }}">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Valyuta</label>
                        <select name="currency" id="currency"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="UZS" {{ old('currency', 'UZS') === 'UZS' ? 'selected' : '' }}>UZS</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                <a href="{{ route('dashboard') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Bekor qilish
                </a>
                <button type="submit"
                        class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Tadbirni yaratish
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('min_participants').addEventListener('input', function() {
    const minVal = parseInt(this.value);
    const maxInput = document.getElementById('max_participants');
    const maxVal = parseInt(maxInput.value);

    if (maxVal < minVal) {
        maxInput.value = minVal;
    }
    maxInput.min = minVal;
});

document.getElementById('start_time').addEventListener('input', function() {
    const startTime = this.value;
    const endTimeInput = document.getElementById('end_time');

    if (startTime) {
        endTimeInput.min = startTime;
        if (endTimeInput.value && endTimeInput.value <= startTime) {
            endTimeInput.value = '';
        }
    }
});

document.getElementById('images').addEventListener('change', function() {
    const files = this.files;
    if (files.length > 5) {
        alert('Ko\'pi bilan 5 ta rasm tanlash mumkin');
        this.value = '';
        return;
    }

    for (let file of files) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Har bir rasm 2MB dan kichik bo\'lishi kerak');
            this.value = '';
            return;
        }
    }
});
</script>
@endpush
@endsection
