@props(['active' => ''])

@php
    $links = [
        [
            'key' => 'help',
            'route' => 'help',
            'label' => 'Trung tâm trợ giúp (FAQ)',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        [
            'key' => 'partner',
            'route' => 'partner-policy',
            'label' => 'Chính sách đối tác chủ sân',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
        ],
        [
            'key' => 'cancellation',
            'route' => 'cancellation-policy',
            'label' => 'Chính sách hủy sân',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        [
            'key' => 'payment',
            'route' => 'payment-policy',
            'label' => 'Chính sách thanh toán',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />',
        ],
        [
            'key' => 'terms',
            'route' => 'terms',
            'label' => 'Điều khoản sử dụng',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        ],
        [
            'key' => 'privacy',
            'route' => 'privacy-policy',
            'label' => 'Chính sách bảo mật',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
        ],
        [
            'key' => 'contact',
            'route' => 'contact',
            'label' => 'Liên hệ hỗ trợ',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        ],
    ];
@endphp

<aside class="lg:col-span-1">
    {{-- Khối sticky bao trùm toàn bộ: mục lục và box hotline nằm cùng 1 dòng chảy, không bao giờ bị đè nhau --}}
    <div class="sticky top-24 space-y-4 max-h-[calc(100vh-7rem)] overflow-y-auto pr-0.5">
        {{-- Card Mục lục --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-5 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-3 px-1">
                Mục lục hỗ trợ
            </h3>
            <nav class="space-y-1">
                @foreach($links as $link)
                    @php $isActive = ($active === $link['key']); @endphp
                    <a href="{{ route($link['route']) }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors {{ $isActive ? 'bg-primary-50 dark:bg-primary-950/50 text-primary-700 dark:text-primary-300 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <svg class="w-4 h-4 shrink-0 {{ $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $link['icon'] !!}
                        </svg>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Box hotline CSKH được gắn liền bên trong cùng khối --}}
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <div class="rounded-xl bg-gradient-to-br from-cta-400/20 via-primary-500/10 to-primary-600/5 border border-cta-400/30 p-3.5 text-zinc-900 dark:text-zinc-100">
                    <h4 class="text-xs font-bold mb-1 flex items-center gap-1.5 text-zinc-900 dark:text-zinc-100">
                        <svg class="w-3.5 h-3.5 text-cta-600 dark:text-cta-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Cần trợ giúp ngay?
                    </h4>
                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 mb-2.5 leading-snug">
                        Đội ngũ CSKH trực tuyến giải quyết phát sinh tại sân thể thao.
                    </p>
                    <a href="tel:{{ str_replace(' ', '', setting('contact_hotline', '1900 1234')) }}"
                        class="inline-flex items-center justify-center gap-1.5 w-full py-2 bg-cta-400 hover:bg-cta-500 active:scale-[0.98] text-zinc-900 font-bold text-xs rounded-lg shadow-xs transition-all">
                        Gọi Hotline: {{ setting('contact_hotline', '1900 1234') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>
