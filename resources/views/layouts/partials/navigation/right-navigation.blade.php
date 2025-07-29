<div class="flex items-center space-x-4">
    @auth
        <!-- Notifications -->
        <div class="relative hidden md:block">
            <button
                id="user-menu-button"
                class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium focus:outline-none"
                aria-expanded="false"
                aria-haspopup="true">
                {{ auth()->user()->name }}
                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            <div
                id="user-menu"
                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="user-menu-button">
                <div class="py-1" role="none">
                    <a href="/profile/show" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        <div class="flex items-center">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profil
                        </div>
                    </a>

                    <div class="border-t border-gray-100 my-1"></div>

                    <div class="px-4 py-2 text-xs text-gray-500">
                        Reyting: {{ auth()->user()->rating }} ball
                    </div>

                    <div class="border-t border-gray-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <div class="flex items-center">
                                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Chiqish
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <a href="events"
            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
            Tadbirlar
        </a>
        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
            Kirish
        </a>
        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
            Ro'yxatdan o'tish
        </a>
    @endauth
</div>
