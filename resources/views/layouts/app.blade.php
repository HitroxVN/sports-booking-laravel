<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Alpine.js (CDN) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">

        {{-- Navbar --}}
        <nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    {{-- Logo + nav links --}}
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-lg font-bold text-green-600 dark:text-green-400">
                            🏟 {{ config('app.name') }}
                        </a>

                        <div class="hidden sm:flex items-center gap-6">
                            <a href="{{ route('home') }}"
                               class="text-sm font-medium {{ request()->routeIs('home') ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                                Trang chủ
                            </a>
                            <a href="#"
                               class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                Tìm sân
                            </a>
                        </div>
                    </div>

                    {{-- Right side --}}
                    <div class="hidden sm:flex items-center gap-4">
                        @auth
                            {{-- Notification bell (Sprint 7) --}}
                            {{-- <x-notification-bell /> --}}

                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                        class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    <span class="font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <div x-show="open" @click.outside="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        📋 Đơn đặt sân của tôi
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        ❤️ Sân yêu thích
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        👤 Hồ sơ
                                    </a>
                                    <hr class="my-1 border-gray-200 dark:border-gray-600">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            🚪 Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}"
                               class="text-sm font-medium px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Đăng ký
                            </a>
                        @endauth
                    </div>

                    {{-- Hamburger --}}
                    <div class="flex items-center sm:hidden">
                        <button @click="open = !open" class="text-gray-500 dark:text-gray-400 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100 dark:border-gray-700">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('home') }}" class="block text-sm text-gray-700 dark:text-gray-300">Trang chủ</a>
                    <a href="#" class="block text-sm text-gray-700 dark:text-gray-300">Tìm sân</a>
                    @auth
                        <a href="#" class="block text-sm text-gray-700 dark:text-gray-300">Đơn đặt sân</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600">Đăng xuất</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block text-sm text-gray-700 dark:text-gray-300">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="block text-sm text-green-600">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                    ✅ {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    ❌ {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Page heading --}}
        @isset($header)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                {{ $header }}
            </div>
        @endisset

        {{-- Page content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="mt-16 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả quyền được bảo lưu.
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>

