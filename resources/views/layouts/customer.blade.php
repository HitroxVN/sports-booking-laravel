<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/logo.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">

    {{-- ================================================
         TOP UTILITY BAR
    ================================================= --}}
    <div class="bg-zinc-900 dark:bg-zinc-950 text-zinc-300 dark:text-zinc-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-4">
            <span>Hotline: <a href="tel:19001234" class="hover:text-white transition-colors">1900 1234</a></span>
            <div class="hidden sm:flex items-center gap-4">
                <a href="/search" class="hover:text-white transition-colors">Tìm sân</a>
                <span class="text-zinc-700 dark:text-zinc-600 select-none">|</span>
                <a href="#" class="hover:text-white transition-colors">Hỗ trợ</a>
                <span class="text-zinc-700 dark:text-zinc-600 select-none">|</span>
                <a href="/register" class="hover:text-white transition-colors">Đăng ký</a>
            </div>
        </div>
    </div>

    {{-- ================================================
         MAIN HEADER
    ================================================= --}}
    <header x-data="{ mobileOpen: false }" class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-6">

                {{-- Wordmark --}}
                <a href="/" class="flex items-center gap-2.5 shrink-0 focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg">
                    <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Arena Sports Booking"
                         class="w-9 h-9 rounded-lg object-cover shrink-0">
                    <div class="leading-tight">
                        <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Arena</span>
                        <span class="block text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Sports Booking</span>
                    </div>
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="/"
                        class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                              {{ request()->is('/') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        Trang chủ
                    </a>
                    <a href="/search"
                        class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                              {{ request()->is('search*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        Tìm sân
                    </a>
                    <a href="/venues/popular"
                        class="px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Sân nổi bật
                    </a>
                </nav>

                {{-- Right: search toggle + auth --}}
                <div class="flex items-center gap-3">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            :aria-expanded="open.toString()"
                            aria-haspopup="true"
                            class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg px-2 py-1.5 transition-colors">
                            <span class="hidden sm:block font-medium">{{ Str::limit(Auth::user()->name, 16) }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-52 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                            <a href="/my-bookings"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg mx-1 transition-colors">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Đơn đặt sân của tôi
                            </a>
                            <a href="/profile"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg mx-1 transition-colors">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Hồ sơ
                            </a>
                            <hr class="my-1 border-zinc-200 dark:border-zinc-700 mx-4">
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg mx-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="/login"
                        class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors hidden sm:block">
                        Đăng nhập
                    </a>
                    <a href="/register"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-150">
                        Đăng ký
                    </a>
                    @endauth

                    {{-- Mobile hamburger --}}
                    <button @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen.toString()"
                        aria-controls="mobile-menu"
                        aria-label="Mở menu"
                        class="lg:hidden text-zinc-500 dark:text-zinc-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div>
            <div id="mobile-menu"
                :class="{'block': mobileOpen, 'hidden': !mobileOpen}"
                class="hidden lg:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="px-4 py-3 space-y-0.5">
                    <a href="/" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Trang chủ</a>
                    <a href="/search" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Tìm sân</a>
                    <a href="/venues/popular" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Sân nổi bật</a>
                    @auth
                    <a href="/my-bookings" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Đơn đặt sân</a>
                    <a href="/profile" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Hồ sơ</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">Đăng xuất</button>
                    </form>
                    @else
                    <a href="/login" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Đăng nhập</a>
                    <a href="/register" class="block px-3 py-2.5 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- ================================================
         FLASH MESSAGES
    ================================================= --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-xl text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- ================================================
         PAGE CONTENT
    ================================================= --}}
    <main>
        @yield('content')
    </main>

    {{-- ================================================
         FOOTER
    ================================================= --}}
    <footer class="bg-zinc-900 dark:bg-zinc-950 text-zinc-400 dark:text-zinc-500 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            {{-- Top row: brand + links --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-zinc-800">

                {{-- Brand column --}}
                <div class="lg:col-span-1">
                    <a href="/" class="flex items-center gap-2.5 mb-4 group">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Arena Sports Booking"
                             class="w-9 h-9 rounded-lg object-cover shrink-0">
                        <div class="leading-tight">
                            <span class="block text-sm font-bold text-white tracking-tight">Arena</span>
                            <span class="block text-[10px] font-medium text-zinc-500 uppercase tracking-widest">Sports Booking</span>
                        </div>
                    </a>
                    <p class="text-sm leading-relaxed text-zinc-500 mb-5 max-w-xs">
                        Nền tảng đặt sân thể thao trực tuyến hàng đầu Việt Nam. Kết nối người chơi với hơn 200 khu sân chất lượng.
                    </p>
                    {{-- Social links --}}
                    <div class="flex items-center gap-3">
                        <a href="#" aria-label="Facebook" class="w-8 h-8 bg-zinc-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="YouTube" class="w-8 h-8 bg-zinc-800 hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                                <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram" class="w-8 h-8 bg-zinc-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke-width="2" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Người dùng --}}
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Người dùng</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/search" class="text-sm hover:text-white transition-colors">Tìm kiếm sân</a></li>
                        <li><a href="/register" class="text-sm hover:text-white transition-colors">Đăng ký tài khoản</a></li>
                        <li><a href="/login" class="text-sm hover:text-white transition-colors">Đăng nhập</a></li>
                        <li><a href="/my-bookings" class="text-sm hover:text-white transition-colors">Đơn đặt sân</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Hướng dẫn sử dụng</a></li>
                    </ul>
                </div>

                {{-- Chủ sân --}}
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Chủ sân</h3>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Đăng ký khu sân</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Quản lý lịch đặt</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Báo cáo doanh thu</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Khuyến mãi</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách hợp tác</a></li>
                    </ul>
                </div>

                {{-- Hỗ trợ --}}
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Hỗ trợ</h3>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách hủy sân</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách thanh toán</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom row --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-zinc-600 dark:text-zinc-600">
                    &copy; {{ date('Y') }} Arena Sports Booking. Tất cả quyền được bảo lưu.
                </p>
                <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-600">
                    <span>Hotline:</span>
                    <a href="tel:19001234" class="hover:text-zinc-400 transition-colors">1900 1234</a>
                    <span class="select-none">&middot;</span>
                    <span>Email:</span>
                    <a href="mailto:hotro@arenasports.vn" class="hover:text-zinc-400 transition-colors">hotro@arenasports.vn</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>