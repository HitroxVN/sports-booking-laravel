{{-- ================================================================
     SITE HEADER — dùng chung mọi trang khách (layouts/customer)
     Phong cách: Nhà thuốc Long Châu (top bar xanh brand + CTA vàng)
     Được dùng qua: <x-site-header />
================================================================= --}}
<div class="bg-primary-600 dark:bg-primary-800 text-white/90 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-4">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-cta-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M2 5a2 2 0 012-2h1.5a1 1 0 01.968.732l1.107 3.857a1 1 0 01-.313 1.047l-1.403 1.194a11.018 11.018 0 005.501 5.501l1.194-1.403a1 1 0 011.047-.313l3.857 1.107A1 1 0 0117 13.5V15a2 2 0 01-2 2h-1C7.72 17 3 12.28 3 6.5V5z" />
            </svg>
            Hotline: <a href="tel:19001234" class="font-semibold text-white hover:text-cta-300 transition-colors">1900 1234</a>
        </span>
        <div class="hidden sm:flex items-center gap-3">
            <a href="/search" class="hover:text-cta-300 transition-colors">Tìm sân</a>
            <span class="text-white/30 select-none" aria-hidden="true">|</span>
            <a href="/lien-he" class="hover:text-cta-300 transition-colors">Hỗ trợ</a>
            <span class="text-white/30 select-none" aria-hidden="true">|</span>
            <a href="/register" class="hover:text-cta-300 transition-colors">Đăng ký</a>
        </div>
    </div>
</div>

<header x-data="{ mobileOpen: false }" class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50 shadow-lc">
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
                          {{ request()->is('/') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30' : 'text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20' }}">
                    Trang chủ
                </a>
                <a href="/search"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                          {{ request()->is('search*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30' : 'text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20' }}">
                    Tìm sân
                </a>
                <a href="/venues/popular"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                          {{ request()->is('venues/popular') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30' : 'text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20' }}">
                    Sân nổi bật
                </a>
                <a href="/lien-he"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                          {{ request()->is('lien-he') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30' : 'text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20' }}">
                    Liên hệ
                </a>
            </nav>

            {{-- Right: auth + theme toggle --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Dark mode toggle --}}
                <button type="button"
                    x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            document.documentElement.classList.toggle('dark', this.dark);
                            try { localStorage.setItem('color-mode', this.dark ? 'dark' : 'light'); } catch (e) {}
                        }
                    }"
                    @click="toggle()"
                    :aria-label="dark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối'"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500">
                    <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.352 8.964a8 8 0 01-9.388-9.388 8 8 0 109.388 9.388z" />
                    </svg>
                    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-cloak>
                        <circle cx="12" cy="12" r="4" stroke-width="2" />
                        <path stroke-linecap="round" stroke-width="2" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                    </svg>
                </button>

                @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                        class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg px-2 py-1.5 transition-colors">
                        <span class="hidden sm:block font-medium">{{ Str::limit(Auth::user()->name, 16) }}</span>
                        @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                            class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-100 dark:ring-primary-900 shrink-0">
                        @else
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold uppercase shrink-0">{{ mb_substr(trim(Auth::user()->name), 0, 1) }}</span>
                        @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-zinc-800 rounded-xl shadow-lc-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                        <a href="/my-bookings"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Đơn đặt sân của tôi
                        </a>
                        <a href="/profile"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Hồ sơ
                        </a>
                        <hr class="my-1 border-zinc-200 dark:border-zinc-700 mx-4">
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg mx-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="/login"
                    class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors hidden sm:block">
                    Đăng nhập
                </a>
                {{-- CTA chính — vàng Long Châu, chữ đen --}}
                <a href="/register"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-cta-400 hover:bg-cta-600 active:scale-[0.98] text-zinc-900 text-sm font-semibold rounded-lg shadow-lc hover:shadow-lc-lg transition-all duration-200">
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
            x-cloak
            :class="{'block': mobileOpen, 'hidden': !mobileOpen}"
            class="hidden lg:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <div class="px-4 py-3 space-y-0.5">
                <a href="/" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Trang chủ</a>
                <a href="/search" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Tìm sân</a>
                <a href="/venues/popular" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Sân nổi bật</a>
                <a href="/lien-he" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Liên hệ</a>
                @auth
                <a href="/my-bookings" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Đơn đặt sân</a>
                <a href="/profile" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Hồ sơ</a>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">Đăng xuất</button>
                </form>
                @else
                <a href="/login" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Đăng nhập</a>
                <a href="/register" class="block px-3 py-2.5 text-sm font-semibold text-zinc-900 dark:text-zinc-100 bg-cta-400 hover:bg-cta-600 rounded-lg transition-colors">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</header>
