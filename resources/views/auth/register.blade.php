@extends('layouts.auth')

@section('title', 'Ro\'yxatdan o\'tish')

@section('content')
<div class="flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Ro'yxatdan o'tish
            </h2>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Ism</label>
                <input id="name" name="name" type="text" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Parol</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="captcha" class="block text-sm font-medium text-gray-700 mb-2">Xavfsizlik kodi</label>
                <div class="flex items-center space-x-3">
                    <div class="flex-1">
                        <input id="captcha" name="captcha" type="text" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('captcha') border-red-500 @enderror"
                               placeholder="Kodni kiriting..." autocomplete="off">
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="border border-gray-300 rounded-md overflow-hidden bg-white">
                            <img id="captcha-image"
                                 src="{{ captcha_src('flat') }}"
                                 alt="Captcha"
                                 class="block cursor-pointer hover:opacity-80 transition-opacity"
                                 style="width: 160px; height: 46px;"
                                 onclick="refreshCaptcha()"
                                 title="Yangi kod olish uchun bosing">
                        </div>
                        <button type="button"
                                onclick="refreshCaptcha()"
                                class="p-2 text-gray-500 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
                                title="Yangi kod">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('captcha')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Rasmda ko'rsatilgan kodni kiriting. Agar ko'rinmasa, rasmni bosing.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Parolni tasdiqlang</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ro'yxatdan o'tish
                </button>
            </div>

            <div class="text-center">
                <p class="mt-2 text-sm text-gray-600">
                    Allaqachon hisobingiz bormi?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Kirish
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
// Include the same JavaScript as login form
function refreshCaptcha() {
    const captchaImage = document.getElementById('captcha-image');
    const captchaInput = document.getElementById('captcha');

    captchaImage.style.opacity = '0.5';
    const newSrc = '/captcha/flat?' + new Date().getTime();
    captchaImage.src = newSrc;
    captchaInput.value = '';
    captchaInput.focus();

    captchaImage.onload = function() {
        captchaImage.style.opacity = '1';
    };
}

document.getElementById('captcha-image').onerror = function() {
    setTimeout(refreshCaptcha, 1000);
};

document.getElementById('captcha').addEventListener('input', function() {
    this.classList.remove('border-red-500');
    const errorMsg = this.parentNode.querySelector('.captcha-error');
    if (errorMsg) {
        errorMsg.remove();
    }
});
</script>
@endpush
@endsection
