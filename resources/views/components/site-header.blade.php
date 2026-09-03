{{-- ================================================================
     SITE HEADER — dùng chung mọi trang khách (layouts/customer)
     Chỉnh sửa giao diện header chỉ cần sửa file này.
     Được dùng qua: <x-site-header />
================================================================= --}}
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

<header x-data="{ mobileOpen: false }" class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-6">

            {{-- Wordmark --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0 focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg">
                <img src="{{ asset('images/logo/logo.jpg') }}" alt="Arena Sports Booking"
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
                <a href="/lien-he"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                          {{ request()->is('lien-he') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                    Liên hệ
                </a>
            </nav>

            {{-- Right: auth --}}
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
                <a href="/lien-he" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">Liên hệ</a>
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