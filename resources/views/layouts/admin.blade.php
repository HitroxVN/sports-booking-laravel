<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/logo.jpg') }}">

    <title>{{ $title ?? 'Admin Panel' }} — Arena Sports Booking</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        {{-- Sidebar — component: components/panel-sidebar.blade.php --}}
        <x-panel-sidebar
            homeUrl="{{ route('admin.dashboard') }}"
            alt="Arena Admin"
            badge="Admin Panel"
            :sections="[
                'Tổng quan' => [
                    ['admin.dashboard', 'home', 'Dashboard'],
                ],
                'Quản lý' => [
                    ['admin.users.index', 'users', 'Người dùng'],
                    ['admin.venues.index', 'building', 'Khu sân'],
                    ['admin.bookings.index', 'calendar', 'Đơn đặt sân'],
                    ['admin.sports.index', 'squares', 'Môn thể thao'],
                ],
                'Nâng cao' => [
                    ['admin.reports.index', 'chart', 'Báo cáo'],
                ],
                'Tài khoản' => [
                    ['profile.edit', 'users', 'Hồ sơ cá nhân'],
                ],
            ]" />

        {{-- Main content --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-auto">

            {{-- Top bar --}}
            <header class="flex items-center justify-between h-16 px-4 sm:px-6 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-10 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Hamburger mobile/tablet --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        aria-label="Mở menu"
                        class="lg:hidden text-zinc-500 dark:text-zinc-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg p-1.5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Page title --}}
                    <h1 class="text-base sm:text-lg font-bold text-zinc-800 dark:text-zinc-200 truncate">
                        {{ $title ?? 'Admin Panel' }}
                    </h1>
                </div>

                {{-- User dropdown --}}
                <div x-data="{ open: false }" class="relative shrink-0">
                    <button @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                        class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-xl pl-2 pr-3 py-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-600 text-white text-xs font-bold shrink-0">
                            {{ mb_substr(trim(Auth::user()->name), 0, 1) }}
                        </span>
                        <span class="hidden sm:block font-medium max-w-32 truncate">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Hồ sơ cá nhân
                        </a>
                        <hr class="my-1 border-zinc-200 dark:border-zinc-700 mx-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg mx-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6">
                @isset($header)
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endisset

                @if(session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
