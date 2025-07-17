<div class="flex items-center">
    <a href="/events" class="flex items-center">
        <svg class="h-8 w-8 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-xl font-bold text-blue-600">Tadbir Platformasi</span>
    </a>

    <!-- Desktop Navigation -->
    <div class="hidden md:ml-10 md:flex md:items-center md:space-x-8">

        @auth
            @if(auth()->user()->isAdmin())
                <a href="/dashboard" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                    Admin Panel
                </a>
            @endif
            <a href="/events/create"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('events.create') ? 'text-blue-600 bg-blue-50' : '' }}">
                Tadbir yaratish
            </a>
            <a href="/dashboard"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                Dashboard
            </a>
        @endauth
    </div>
</div>
