<footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <svg class="h-8 w-8 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xl font-bold">Tadbir Platformasi</span>
                </div>
                <p class="text-gray-300 text-sm max-w-md">
                    O'zbekistondagi eng yirik tadbir tashkil qilish platformasi. Har xil tadbirlarni yarating, qatnashing va yangi odamlar bilan tanishing.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-200 uppercase tracking-wider mb-4">Havolalar</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('events.index') }}" class="text-gray-300 hover:text-white text-sm transition-colors">Tadbirlar</a></li>
                    @auth
                        <li><a href="/events/create" class="text-gray-300 hover:text-white text-sm transition-colors">Tadbir yaratish</a></li>
                        <li><a href="/dashboard" class="text-gray-300 hover:text-white text-sm transition-colors">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm transition-colors">Kirish</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-white text-sm transition-colors">Ro'yxatdan o'tish</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-200 uppercase tracking-wider mb-4">Yordam</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Yordam markazi</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Bog'lanish</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Maxfiylik siyosati</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Foydalanish shartlari</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} Tadbir Platformasi. Barcha huquqlar himoyalangan.
            </p>
        </div>
    </div>
</footer>
