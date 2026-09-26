@extends('layouts.customer')

@section('content')

{{-- ====================================================
     HERO CAROUSEL
==================================================== --}}
<section class="bg-tint-sky dark:bg-zinc-950">
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
<section class="bg-tint-sky dark:bg-zinc-950 pb-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="/search" method="GET"
              class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-lc p-2 sm:p-3 flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <label for="hero-search" class="sr-only">Tìm sân</label>
                <input id="hero-search" type="text" name="q"
                       placeholder="Tên sân, quận, thành phố..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-tint-sky/60 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 border border-transparent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors">
            </div>
            <div class="relative sm:w-56">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 4v12a2 2 0 002 2h8a2 2 0 002-2V4M10 12h4"/>
                </svg>
                <label for="hero-sport" class="sr-only">Môn thể thao</label>
                <select id="hero-sport" name="sport_id"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-tint-sky/60 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 border border-transparent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors appearance-none">
                    <option value="">Tất cả môn thể thao</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-6 py-2.5 bg-cta-400 hover:bg-cta-600 active:scale-[0.98] text-zinc-900 text-sm font-semibold rounded-xl shadow-lc hover:shadow-lc-lg transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tìm kiếm
            </button>
        </form>
    </div>
</section>

{{-- ====================================================
     VALUE PROPS
==================================================== --}}
<section class="bg-tint-sky dark:bg-zinc-950 py-12 sm:py-16">
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
<section class="bg-tint-sky dark:bg-zinc-950 pb-12 sm:pb-16">
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
     FEATURED VENUES — HALL OF FAME: BỤC VINH DANH
==================================================== --}}
@php
    $champion = $featuredVenues->first();
    $silver   = $featuredVenues->skip(1)->first();
    $bronze   = $featuredVenues->skip(2)->first();
    $theRest  = $featuredVenues->skip(3);
    $maxBookings = max(1, $featuredVenues->max('bookings_count') ?? 0);
@endphp
<section class="relative overflow-hidden bg-gradient-to-b from-primary-950 via-primary-900 to-primary-950 py-16 sm:py-24">
    {{-- Sân khấu ánh sáng: hào quang vàng rọi giữa + 2 đèn biên + lưới nền --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[52rem] h-[30rem] lc-halo bg-[radial-gradient(closest-side,rgba(255,205,0,0.22),transparent_70%)]"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-primary-600/25 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-cta-400/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.16] [background-image:radial-gradient(rgba(255,255,255,0.55)_1px,transparent_1px)] [background-size:26px_26px]"></div>
    </div>

    {{-- Sao lấp lánh rải quanh bục --}}
    <div class="pointer-events-none absolute inset-0 hidden sm:block" aria-hidden="true">
        <svg class="lc-sparkle absolute top-[12%] left-[8%] w-3 h-3 text-cta-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
        <svg class="lc-sparkle lc-sparkle--d1 absolute top-[22%] right-[12%] w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
        <svg class="lc-sparkle lc-sparkle--d2 absolute bottom-[30%] left-[15%] w-2.5 h-2.5 text-cta-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
        <svg class="lc-sparkle lc-sparkle--d3 absolute bottom-[18%] right-[18%] w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
        <svg class="lc-sparkle lc-sparkle--d4 absolute top-[8%] left-[45%] w-2 h-2 text-cta-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 lc-reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cta-400/10 border border-cta-400/30 backdrop-blur-sm text-cta-300 text-xs font-bold uppercase tracking-widest">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/></svg>
                Bảng xếp hạng theo lượt đặt thật
            </span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Vinh danh khu sân
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cta-300 via-cta-400 to-cta-500">nổi bật nhất</span>
            </h2>
            <p class="mt-3 text-sm sm:text-base text-white/60 leading-relaxed">
                Top 5 được xếp hạng thật từ hàng nghìn lượt đặt của người chơi — không bôi đen, không đề cử.
            </p>
        </div>

        @if($champion)
            @php
                $podiumEntries = collect([$silver, $champion, $bronze])->filter();
                $podiumCount = $podiumEntries->count();
            @endphp
            {{-- ── BỤC PODIUM: Hạng 2 · Quán quân · Hạng 3 (desktop: bục giữa cao nhất) ── --}}
            <div class="lc-reveal grid grid-cols-1 {{ $podiumCount === 3 ? 'md:grid-cols-3 md:items-end' : 'max-w-3xl mx-auto md:grid-cols-2 md:items-end' }} gap-5 md:gap-6">

                {{-- HẠNG 2 · BẠC --}}
                @if($silver)
                    @php
                        $minPrice2 = $silver->courts->flatMap(fn($c) => $c->slots)->min('price');
                        $hot2 = min(100, (int) round($silver->bookings_count / $maxBookings * 100));
                    @endphp
                    <a href="/venues/{{ $silver->slug }}"
                       class="group relative rounded-3xl overflow-hidden bg-white/[0.06] backdrop-blur-sm border border-white/10 hover:border-white/30 hover:-translate-y-1.5 transition-all duration-300">
                        <div class="relative h-40 sm:h-48 overflow-hidden">
                            @if($silver->cover_image)
                                <img src="{{ asset('storage/' . $silver->cover_image) }}" alt="{{ $silver->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-800/70 to-primary-950 text-white/25">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-950/85 via-primary-950/25 to-transparent" aria-hidden="true"></div>
                            <span class="absolute top-4 left-4 w-10 h-10 rounded-full bg-gradient-to-br from-white via-zinc-200 to-zinc-400 text-zinc-800 text-sm font-extrabold flex items-center justify-center border-2 border-white/70 shadow-lg">2</span>
                            <span class="absolute top-5 right-4 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white text-[11px] font-bold">Hạng nhì</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-base text-white line-clamp-1 group-hover:text-cta-300 transition-colors">{{ $silver->name }}</h3>
                            <p class="text-xs text-white/50 mt-1 line-clamp-1">{{ $silver->district }}, {{ $silver->city }}</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="flex items-center gap-1 text-xs font-semibold text-white/80">
                                    <svg class="w-3.5 h-3.5 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                                    {{ number_format($silver->bookings_count) }} lượt đặt
                                </span>
                                @if($minPrice2)
                                    <span class="text-sm font-extrabold text-white"><span class="text-[10px] text-white/50 font-medium">từ </span>{{ number_format($minPrice2, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="text-xs text-white/50">Liên hệ</span>
                                @endif
                            </div>
                            <div class="mt-3 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                <div class="lc-bar h-full rounded-full bg-gradient-to-r from-zinc-200 to-zinc-400" style="width: {{ $hot2 }}%"></div>
                            </div>
                        </div>
                    </a>
                @endif

                {{-- HẠNG 1 · QUÁN QUÂN (bục cao nhất, vương miện + hào quang vàng) --}}
                <div class="relative">
                    <div class="lc-float absolute -top-7 left-1/2 -translate-x-1/2 z-10 pointer-events-none" aria-hidden="true">
                        <svg class="w-12 h-12 drop-shadow-[0_4px_14px_rgba(255,205,0,0.65)]" viewBox="0 0 24 24" fill="url(#lc-crown-gold)">
                            <defs>
                                <linearGradient id="lc-crown-gold" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#FFE382"/>
                                    <stop offset="1" stop-color="#F5B800"/>
                                </linearGradient>
                            </defs>
                            <path d="M3 17.5l-1.2-9.2a.5.5 0 0 1 .8-.46L7.4 11 11.5 5a.6.6 0 0 1 1 0l4.1 6 4.8-3.16a.5.5 0 0 1 .8.46L21 17.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
                            <rect x="3.5" y="20" width="17" height="2" rx="1"/>
                        </svg>
                    </div>
                    <a href="/venues/{{ $champion->slug }}"
                       class="group relative block rounded-3xl overflow-hidden bg-white/[0.08] backdrop-blur-sm border-2 border-cta-400/60 shadow-[0_0_70px_-18px_rgba(255,205,0,0.55)] hover:border-cta-400 hover:-translate-y-2 transition-all duration-300 lc-gold-sweep">
                        <div class="relative h-64 sm:h-96 overflow-hidden">

                            @if($champion->cover_image)
                                <img src="{{ asset('storage/' . $champion->cover_image) }}" alt="{{ $champion->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-cta-400/25 via-primary-800/60 to-primary-950 text-cta-300/70">
                                    <svg class="lc-float w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4M7 4h10v5a5 5 0 0 1-10 0V4zM7 6H4a2 2 0 0 0 2 5h1m10-5h3a2 2 0 0 1-2 5h-1"/>
                                    </svg>
                                    <span class="text-xs mt-2 text-white/40">Khu sân được yêu thích nhất</span>
                                </div>
                            @endif
                            {{-- Lớp phủ gradient + ánh vàng hất từ trên xuống --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-950/90 via-primary-950/30 to-transparent" aria-hidden="true"></div>
                            <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-cta-400/25 to-transparent" aria-hidden="true"></div>

                            {{-- Băng huy hiệu quán quân --}}
                            <span class="absolute top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-gradient-to-r from-cta-400 to-cta-600 text-zinc-900 text-[11px] font-extrabold uppercase tracking-widest shadow-lg whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                                Quán quân
                            </span>

                            {{-- Thông tin nổi dưới ảnh, căn giữa --}}
                            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-center">
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white line-clamp-1">{{ $champion->name }}</h3>
                                <p class="text-xs sm:text-sm text-white/60 mt-1 line-clamp-1">{{ $champion->district }}, {{ $champion->city }}</p>

                                {{-- 3 chỉ số chính --}}
                                <div class="mt-3 flex flex-wrap justify-center items-center gap-x-4 gap-y-1.5">
                                    <span class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-white">
                                        <svg class="w-4 h-4 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                                        </svg>
                                        {{ number_format($champion->bookings_count) }} lượt đặt
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-white">
                                        <svg class="w-4 h-4 text-cta-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ number_format((float) $champion->rating_avg, 1) }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-white">
                                        <svg class="w-4 h-4 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2 M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2 M10 6h4 M10 10h4 M10 14h4 M10 18h4"/>
                                        </svg>
                                        {{ $champion->courts_count }} sân
                                    </span>
                                </div>

                                {{-- CTA vàng --}}
                                <div class="mt-4">
                                    <span class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-cta-400 group-hover:bg-cta-300 text-zinc-900 text-sm font-bold shadow-lg transition-colors">
                                        Đặt sân quán quân
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                </a>
                </div>

                {{-- HẠNG 3 · ĐỒNG --}}
                @if($bronze)
                    @php
                        $minPrice3 = $bronze->courts->flatMap(fn($c) => $c->slots)->min('price');
                        $hot3 = min(100, (int) round($bronze->bookings_count / $maxBookings * 100));
                    @endphp
                    <a href="/venues/{{ $bronze->slug }}"
                       class="group relative rounded-3xl overflow-hidden bg-white/[0.06] backdrop-blur-sm border border-white/10 hover:border-white/30 hover:-translate-y-1.5 transition-all duration-300">
                        <div class="relative h-40 sm:h-48 overflow-hidden">
                            @if($bronze->cover_image)
                                <img src="{{ asset('storage/' . $bronze->cover_image) }}" alt="{{ $bronze->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-accent-brown/40 to-primary-950 text-amber-400/40">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-primary-950/85 via-primary-950/25 to-transparent" aria-hidden="true"></div>
                            <span class="absolute top-4 left-4 w-10 h-10 rounded-full bg-gradient-to-br from-amber-200 via-amber-500 to-accent-brown text-white text-sm font-extrabold flex items-center justify-center border-2 border-amber-200/60 shadow-lg">3</span>
                            <span class="absolute top-5 right-4 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white text-[11px] font-bold">Hạng ba</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-base text-white line-clamp-1 group-hover:text-cta-300 transition-colors">{{ $bronze->name }}</h3>
                            <p class="text-xs text-white/50 mt-1 line-clamp-1">{{ $bronze->district }}, {{ $bronze->city }}</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="flex items-center gap-1 text-xs font-semibold text-white/80">
                                    <svg class="w-3.5 h-3.5 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                                    {{ number_format($bronze->bookings_count) }} lượt đặt
                                </span>
                                @if($minPrice3)
                                    <span class="text-sm font-extrabold text-white"><span class="text-[10px] text-white/50 font-medium">từ </span>{{ number_format($minPrice3, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="text-xs text-white/50">Liên hệ</span>
                                @endif
                            </div>
                            <div class="mt-3 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                <div class="lc-bar h-full rounded-full bg-gradient-to-r from-amber-300 to-amber-600" style="width: {{ $hot3 }}%"></div>
                            </div>
                        </div>
                    </a>
                @endif
            </div>

            {{-- ── HẠNG 4-5 · TIẾP THEO BỤC VINH DANH ── --}}
            @if($theRest->isNotEmpty())
                <div class="lc-reveal mt-5 sm:mt-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($theRest->values() as $key => $venue)
                        @php
                            $rank = $key + 4;
                            $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price');
                            $hotPercent = min(100, (int) round($venue->bookings_count / $maxBookings * 100));
                        @endphp
                        <a href="/venues/{{ $venue->slug }}"
                           class="group flex items-stretch gap-4 rounded-2xl overflow-hidden bg-white/[0.06] backdrop-blur-sm border border-white/10 hover:border-white/30 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-12 shrink-0 flex items-center justify-center bg-white/10 border-r border-white/10">
                                <span class="text-lg font-extrabold text-white/70">{{ $rank }}</span>
                            </div>
                            <div class="w-24 sm:w-28 shrink-0 overflow-hidden bg-primary-900">
                                @if($venue->cover_image)
                                    <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-800/70 to-primary-950 text-white/25">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 p-4 flex flex-col justify-center gap-1.5">
                                <h3 class="font-bold text-sm text-white line-clamp-1 group-hover:text-cta-300 transition-colors">{{ $venue->name }}</h3>
                                <p class="text-[11px] text-white/50 line-clamp-1">{{ $venue->address_line }}</p>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[11px] font-semibold text-white/70 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-cta-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                                        {{ number_format($venue->bookings_count) }} lượt đặt
                                    </span>
                                    @if($minPrice)
                                        <span class="text-xs font-extrabold text-white"><span class="text-[10px] text-white/50 font-medium">từ </span>{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                    @else
                                        <span class="text-[11px] text-white/50">Liên hệ</span>
                                    @endif
                                </div>
                                <div class="h-1 rounded-full bg-white/10 overflow-hidden">
                                    <div class="lc-bar h-full rounded-full bg-gradient-to-r from-cta-400 to-amber-500" style="width: {{ $hotPercent }}%"></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Link bảng xếp hạng đầy đủ --}}
            <div class="lc-reveal mt-10 text-center">
                <a href="{{ route('venues.popular') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cta-400 hover:bg-cta-300 text-zinc-900 text-sm font-bold shadow-lg shadow-cta-400/20 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4M7 4h10v5a5 5 0 0 1-10 0V4zM7 6H4a2 2 0 0 0 2 5h1m10-5h3a2 2 0 0 1-2 5h-1"/></svg>
                    Xem bảng xếp hạng đầy đủ
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @else
            <div class="lc-reveal bg-white/5 backdrop-blur-sm rounded-2xl border border-dashed border-white/15 p-12 text-center">
                <p class="text-sm text-white/50">Hiện chưa có khu sân nổi bật nào được cập nhật.</p>
            </div>
        @endif
    </div>
</section>

{{-- ====================================================
     SPORTS NEWS — TIN MỚI NHẤT TỪ RSS
==================================================== --}}
<section class="bg-tint-sky py-12 sm:py-16 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="text-sm font-bold uppercase tracking-widest text-primary-600 dark:text-primary-400">Nhịp đập thể thao</span>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-zinc-950 sm:text-3xl dark:text-white">Tin mới nhất</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Cập nhật mỗi 30 phút từ các nguồn tin thể thao uy tín.</p>
            </div>
            <a href="{{ route('customer.news.index') }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-bold text-primary-600 transition-colors hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                Xem tất cả tin tức <span aria-hidden="true">→</span>
            </a>
        </div>

        @if($latestNews->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($latestNews as $article)
                    <x-news-card :article="$article" />
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white px-6 py-10 text-center shadow-lc dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Tin thể thao đang được cập nhật. Mời bạn quay lại sau.</p>
            </div>
        @endif
    </div>
</section>

{{-- ====================================================
     CTA STRIP
==================================================== --}}
<section class="bg-tint-sky dark:bg-zinc-950 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-primary-600 rounded-2xl px-6 sm:px-10 py-10 sm:py-12 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 shadow-lc-lg">
            <div class="max-w-xl">
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                    Bạn là chủ sân? Cùng Arena tăng trưởng doanh thu
                </h2>
                <p class="text-sm text-white/80 mt-2 leading-relaxed">
                    Đăng ký miễn phí, kết nối với hàng nghìn người chơi và tối ưu công suất sân trống chỉ trong vài phút.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="/register" class="inline-flex items-center gap-2 px-5 py-2.5 bg-cta-400 text-zinc-900 hover:bg-cta-600 text-sm font-semibold rounded-xl shadow-lc transition-all duration-200">
                    Đăng ký khu sân
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="/lien-he" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl border border-white/20 transition-colors">
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     CONTACT — dùng chung qua components/contact-section
==================================================== --}}
<x-contact-section />

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

    /* Hall of Fame: reveal bục podium + thanh độ hot khi cuộn tới */
    var revealEls = document.querySelectorAll('.lc-reveal');
    if ('IntersectionObserver' in window && revealEls.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { io.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }
});
</script>
@endpush
