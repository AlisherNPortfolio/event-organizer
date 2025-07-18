@extends('layouts.auth')

@section('title', 'Kirish')

@section('content')
<div class="flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Tizimga kirish
            </h2>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf

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
                                 src="{{ captcha_src('default') }}"
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

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Meni eslab qol
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Parolni unutdingizmi?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Kirish
                </button>
            </div>

            <div class="text-center">
                <p class="mt-2 text-sm text-gray-600">
                    Hisobingiz yo'qmi?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Ro'yxatdan o'ting
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
// Captcha refresh function
function refreshCaptcha() {
    const captchaImage = document.getElementById('captcha-image');
    const captchaInput = document.getElementById('captcha');

    // Add loading state
    captchaImage.style.opacity = '0.5';

    // Generate new captcha URL with timestamp to prevent caching
    const newSrc = '/captcha/flat?' + new Date().getTime();

    // Update image source
    captchaImage.src = newSrc;

    // Clear input
    captchaInput.value = '';
    captchaInput.focus();

    // Remove loading state when image loads
    captchaImage.onload = function() {
        captchaImage.style.opacity = '1';
    };
}

// Auto-refresh captcha if it fails to load
document.getElementById('captcha-image').onerror = function() {
    setTimeout(refreshCaptcha, 1000);
};

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const captchaInput = document.getElementById('captcha');
    const submitButton = this.querySelector('button[type="submit"]');

    // Check if captcha is filled
    if (!captchaInput.value.trim()) {
        e.preventDefault();
        captchaInput.focus();
        captchaInput.classList.add('border-red-500');

        // Show error message
        let errorMsg = captchaInput.parentNode.querySelector('.captcha-error');
        if (!errorMsg) {
            errorMsg = document.createElement('p');
            errorMsg.className = 'mt-1 text-sm text-red-600 captcha-error';
            errorMsg.textContent = 'Captcha kodi kiritish majburiy';
            captchaInput.parentNode.appendChild(errorMsg);
        }
        return false;
    }

    // Add loading state to submit button
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            Yuklanmoqda...
        `;

        // Re-enable after 10 seconds to prevent permanent disable
        setTimeout(() => {
            if (submitButton.disabled) {
                submitButton.disabled = false;
                submitButton.innerHTML = `
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    Kirish
                `;
            }
        }, 10000);
    }
});

// Remove error styling when user types
document.getElementById('captcha').addEventListener('input', function() {
    this.classList.remove('border-red-500');
    const errorMsg = this.parentNode.querySelector('.captcha-error');
    if (errorMsg) {
        errorMsg.remove();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // F5 or Ctrl+R to refresh captcha
    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
        e.preventDefault();
        refreshCaptcha();
    }
});
</script>
@endpush
@endsection
