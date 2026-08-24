<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin — ' . config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Alpine.js (CDN) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

            {{-- Sidebar overlay mobile --}}
            <div x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-20 bg-black bg-opacity-50 sm:hidden"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 dark:bg-gray-950
                          transform transition-transform duration-200 ease-in-out sm:translate-x-0 sm:static sm:inset-auto">

                {{-- Logo --}}
                <div class="flex items-center justify-center h-16 border-b border-gray-700">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-white">
                        ⚙️ Admin Panel
                    </a>
                </div>

                {{-- Nav --}}
                <nav class="mt-4 px-3 space-y-1">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard', 'icon' => '📊', 'label' => 'Tổng quan'],
                            ['route' => '#',               'icon' => '👥', 'label' => 'Người dùng'],
                            ['route' => '#',               'icon' => '🏟', 'label' => 'Khu sân'],
                            ['route' => '#',               'icon' => '📋', 'label' => 'Đơn đặt sân'],
                            ['route' => '#',               'icon' => '⚽', 'label' => 'Môn thể thao'],
                            ['route' => '#',               'icon' => '📈', 'label' => 'Báo cáo'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a href="{{ $link['route'] === '#' ? '#' : route($link['route']) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                                  {{ $link['route'] !== '#' && request()->routeIs($link['route'])
                                     ? 'bg-gray-700 text-white'
                                     : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            {{-- Main content --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-auto">

                {{-- Top bar --}}
                <header class="flex items-center justify-between h-16 px-4 sm:px-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="text-gray-500 sm:hidden focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                        {{ $title ?? 'Admin Panel' }}
                    </h1>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            <span class="font-medium">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                             class="absolute right-0 mt-2 w-44 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    🚪 Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
