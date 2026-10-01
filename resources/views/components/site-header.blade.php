{{-- ================================================================
     SITE HEADER — dùng chung mọi trang khách (layouts/customer)
     Phong cách: Nhà thuốc Long Châu (CTA vàng)
     Được dùng qua: <x-site-header />
================================================================= --}}
<header x-data="{ mobileOpen: false }" class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50 shadow-lc">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-6">

            {{-- Wordmark --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0 focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg">
                <img src="{{ asset('images/logo/logo.jpg') }}" alt="Arena Sports Booking"
                    class="w-9 h-9 rounded-lg object-cover shrink-0">
                <div class="leading-tight">
                    <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ setting('site_name', 'Arena') }}</span>
                    <span class="block text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">{{ setting('site_tagline', 'Sports Booking') }}</span>
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
                <a href="{{ route('customer.news.index') }}"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                          {{ request()->is('tin-tuc*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30' : 'text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20' }}">
                    Tin tức
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
                <x-theme-toggle />

                @auth
                @if(Auth::user()->isOwner())
                {{-- Nút chuyển nhanh về Dashboard Chủ sân (như Kênh người bán của Shopee) --}}
                <a href="{{ route('owner.dashboard') }}"
                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors shadow-xs"
                    title="Về trang quản lý sân">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Kênh chủ sân
                </a>
                @elseif(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors shadow-xs"
                    title="Về trang quản trị">
                    Trang quản trị
                </a>
                @endif

                <x-notification-bell />

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
                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-800 rounded-xl shadow-lc-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                        
                        @if(Auth::user()->isOwner())
                        <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-700/60 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200">
                                Tài khoản Chủ sân
                            </span>
                        </div>
                        <a href="{{ route('owner.dashboard') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg mx-1 font-medium transition-colors">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Kênh chủ sân (Dashboard)
                        </a>
                        <a href="{{ route('owner.venues.index') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Khu sân của tôi
                        </a>
                        <a href="{{ route('owner.bookings.index') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Quản lý lịch đặt
                        </a>
                        @elseif(Auth::user()->isAdmin())
                        <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-700/60 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200">
                                Quản trị viên
                            </span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 rounded-lg mx-1 font-medium transition-colors">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Trang quản trị (Admin)
                        </a>
                        @else
                        <a href="/my-bookings"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg mx-1 transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Đơn đặt sân của tôi
                        </a>
                        @endif

                        <a href="{{ route('profile.edit') }}"
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
                <a href="{{ route('customer.news.index') }}" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Tin tức</a>
                <a href="/lien-he" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Liên hệ</a>
                @auth
                @if(Auth::user()->isOwner())
                <a href="{{ route('owner.dashboard') }}" class="block px-3 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg transition-colors">
                    Kênh chủ sân (Dashboard)
                </a>
                <a href="{{ route('owner.venues.index') }}" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">
                    Khu sân của tôi
                </a>
                @elseif(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2.5 text-sm font-semibold text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 rounded-lg transition-colors">
                    Trang quản trị (Admin)
                </a>
                @else
                <a href="/my-bookings" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">
                    Đơn đặt sân
                </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 rounded-lg transition-colors">Hồ sơ</a>
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
