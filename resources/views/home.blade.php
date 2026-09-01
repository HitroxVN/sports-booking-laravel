@extends('layouts.customer')

@section('content')

{{-- ====================================================
     HERO CAROUSEL
==================================================== --}}
<section class="bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="swiper arena-hero-swiper rounded-2xl overflow-hidden shadow-sm">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                    @php
                        // Ảnh phủ toàn bộ slide; text và button nổi đè lên trên kèm lớp phủ tối
                        $hasImage = !empty($banner['image_url']);
                    @endphp
                    <div class="swiper-slide">
                        <div class="arena-slide arena-slide--{{ $banner['theme'] }} {{ $hasImage ? 'arena-slide--has-image' : '' }}"
                             @if($hasImage)
                             style="background-image: url('{{ $banner['image_url'] }}');"
                             @endif
                             role="img"
                             @if($hasImage)
                             aria-label="{{ $banner['title'] }}"
                             @endif>
                            {{-- Text content (nổi trên ảnh + lớp phủ tối) --}}
                            <div class="arena-slide__content">
                                @if(!empty($banner['eyebrow']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-widest bg-white/15 text-white backdrop-blur-sm">
                                        {{ $banner['eyebrow'] }}
                                    </span>
                                @endif
                                <h1 class="arena-slide__title">{{ $banner['title'] }}</h1>
                                <p class="arena-slide__subtitle">{{ $banner['subtitle'] }}</p>
                                <div class="arena-slide__cta">
                                    <a href="{{ $banner['cta_href'] }}" class="arena-btn arena-btn--primary">
                                        {{ $banner['cta_label'] }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                    <a href="#" class="arena-btn arena-btn--ghost">Tìm hiểu thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination dots --}}
            <div class="swiper-pagination arena-pagination"></div>

            {{-- Navigation --}}
            <div class="arena-nav arena-nav--prev">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <div class="arena-nav arena-nav--next">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     QUICK SEARCH BAR
==================================================== --}}
<section class="bg-zinc-50 dark:bg-zinc-950 pb-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="/search" method="GET"
              class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-2 sm:p-3 flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <label for="hero-search" class="sr-only">Tìm sân</label>
                <input id="hero-search" type="text" name="q"
                       placeholder="Tên sân, quận, thành phố..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 border border-transparent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors">
            </div>
            <div class="relative sm:w-56">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 4v12a2 2 0 002 2h8a2 2 0 002-2V4M10 12h4"/>
                </svg>
                <label for="hero-sport" class="sr-only">Môn thể thao</label>
                <select id="hero-sport" name="sport_id"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 border border-transparent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors appearance-none">
                    <option value="">Tất cả môn thể thao</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tìm kiếm
            </button>
        </form>
    </div>
</section>

{{-- ====================================================
     VALUE PROPS
==================================================== --}}
<section class="bg-zinc-50 dark:bg-zinc-950 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-zinc-200 dark:bg-zinc-800 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
            @foreach($valueProps as $vp)
                <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center">
                        @if($loop->index === 0)
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @elseif($loop->index === 1)
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-1">{{ $vp['title'] }}</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $vp['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================================================
     SPORTS CATEGORIES
==================================================== --}}
<section class="bg-zinc-50 dark:bg-zinc-950 pb-12 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                    Chọn môn thể thao của bạn
                </h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Hơn 10 môn thể thao được hỗ trợ trên toàn quốc
                </p>
            </div>
            <a href="/search" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-colors">
                Xem tất cả
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($sports->count() > 0)
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
                @foreach($sports as $sport)
                    <a href="/search?sport_id={{ $sport->id }}"
                       class="arena-sport-tile group">
                        <div class="arena-sport-tile__icon">
                            @if($sport->icon && \Storage::disk('public')->exists($sport->icon))
                                <img src="{{ asset('storage/' . $sport->icon) }}" alt="{{ $sport->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="arena-sport-tile__label">{{ $sport->name }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ====================================================
     FEATURED VENUES
==================================================== --}}
<section class="bg-white dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-800 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                    Khu sân nổi bật
                </h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Những khu sân được đánh giá cao nhất tuần này
                </p>
            </div>
            <a href="/search" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-colors">
                Xem tất cả
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($featuredVenues->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($featuredVenues as $venue)
                    <a href="/venues/{{ $venue->slug }}" class="arena-venue-card group">
                        <div class="arena-venue-card__cover">
                            @if($venue->cover_image)
                                <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="arena-venue-card__placeholder">
                                    <svg class="w-8 h-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            @php $courtCount = $venue->courts->where('status','active')->count(); @endphp
                            @if($courtCount > 0)
                                <span class="absolute top-3 right-3 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm text-zinc-700 dark:text-zinc-200 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    {{ $courtCount }} sân
                                </span>
                            @endif
                        </div>

                        <div class="arena-venue-card__body">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $venue->name }}
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-1 flex items-center gap-1">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="truncate">{{ $venue->district }}, {{ $venue->city }}</span>
                            </p>

                            <div class="arena-venue-card__footer">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">{{ number_format($venue->rating_avg ?? 0, 1) }}</span>
                                </div>
                                @php $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price'); @endphp
                                <div class="text-right">
                                    @if($minPrice)
                                        <span class="block text-[10px] text-zinc-400 dark:text-zinc-500">từ</span>
                                        <span class="block text-sm font-bold text-primary-600 dark:text-primary-400">
                                            {{ number_format($minPrice, 0, ',', '.') }}đ
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Liên hệ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 p-12 text-center">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Hiện chưa có khu sân nổi bật nào được cập nhật.</p>
            </div>
        @endif
    </div>
</section>

{{-- ====================================================
     CTA STRIP
==================================================== --}}
<section class="bg-zinc-50 dark:bg-zinc-950 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-primary-600 rounded-2xl px-6 sm:px-10 py-10 sm:py-12 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="max-w-xl">
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                    Bạn là chủ sân? Cùng Arena tăng trưởng doanh thu
                </h2>
                <p class="text-sm text-white/80 mt-2 leading-relaxed">
                    Đăng ký miễn phí, kết nối với hàng nghìn người chơi và tối ưu công suất sân trống chỉ trong vài phút.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="/register" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-primary-700 hover:bg-zinc-50 text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    Đăng ký khu sân
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl border border-white/20 transition-colors">
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
{{-- Swiper JS --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.arena-hero-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },
        speed: 700,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        pagination: {
            el: '.arena-pagination',
            clickable: true
        },
        navigation: {
            prevEl: '.arena-nav--prev',
            nextEl: '.arena-nav--next'
        }
    });
});
</script>
@endpush
