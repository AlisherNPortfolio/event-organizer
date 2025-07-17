<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tadbir Platformasi')</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-50">
    <div id="app">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and main nav -->
                    @include('layouts.partials.navigation.logo-navigation')

                    <!-- Right side navigation -->
                    @include('layouts.partials.navigation.right-navigation')
                    {{-- @include('layouts.partials.navigation.mobile-menu') --}}
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @include('layouts.partials.flushes.success-flush')

        @include('layouts.partials.flushes.any-flush')

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <notifications position="bottom right" />
    </div>

    <!-- Global JavaScript variables -->
    @include('layouts.partials.scripts')
</body>
</html>
