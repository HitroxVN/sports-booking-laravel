@extends('layouts.customer')

@section('title', 'Khu sân nổi bật')

@section('content')

@php
    // Thống kê tổng cho hero
    $totalVenues  = $podium->count() + $rest->count();
    $totalBookings = $podium->sum('bookings_count') + $rest->sum('bookings_count');
    // Mốc để vẽ thanh "độ hot" theo lượt đặt
    $maxBookings  = max(1, $podium->max('bookings_count') ?? 0, $rest->max('bookings_count') ?? 0);
    $activeSport   = $sports->firstWhere('id', request('sport_id'));
@endphp

{{-- ═══════════════ HERO: BẢNG XẾP HẠNG ═══════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-950">
    {{-- Ánh vàng trang trí kiểu arena-slide --}}
    <div class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 rounded-full bg-cta-400/20 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-cta-300/10 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]" aria-hidden="true"
         style="background-image: radial-gradient(circle at 1px 1px, #ffffff 1px, transparent 0); background-size: 28px 28px;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-16 sm:pt-20 sm:pb-24">
        <div class="grid lg:grid-cols-5 gap-10 lg:items-center">

            {{-- Cột trái: tiêu đề + thống kê --}}
            <div class="lg:col-span-3 text-center lg:text-left">
                <p class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-cta-300 border border-white/15 backdrop-blur-sm mb-5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                    </svg>
                    Bảng xếp hạng khu sân
                </p>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Khu sân được
                    <span class="relative inline-block">
                        <span class="relative z-10 text-cta-300">đặt nhiều nhất</span>
                        <span class="absolute left-0 right-0 bottom-1 h-3 sm:h-4 bg-cta-400/25 rounded-lg -rotate-1" aria-hidden="true"></span>
                    </span>
                </h1>

                <p class="mt-4 text-base sm:text-lg text-white/70 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Xếp hạng theo số lượt đặt thật của cộng đồng người chơi — không theo quảng cáo.
                    Chọn ngay khu sân HOT nhất và tranh slot trước khi hết!
                </p>

                {{-- Thống kê --}}
                <div class="mt-8 flex flex-wrap justify-center lg:justify-start gap-3">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2 M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2 M10 6h4 M10 10h4 M10 14h4 M10 18h4"/>
                        </svg>
                        <div class="text-left">
                            <span class="block text-lg font-extrabold text-white leading-none">{{ number_format($totalVenues) }}</span>
                            <span class="text-[11px] text-white/60">khu sân tham gia</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                        </svg>
                        <div class="text-left">
                            <span class="block text-lg font-extrabold text-white leading-none">{{ number_format($totalBookings) }}</span>
                            <span class="text-[11px] text-white/60">lượt đặt thành công</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-cta-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                        </svg>
                        <div class="text-left">
                            <span class="block text-lg font-extrabold text-white leading-none">{{ number_format($sports->count()) }}</span>
                            <span class="text-[11px] text-white/60">môn thể thao</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: thẻ quán quân (glass) --}}
            @if($podium->isNotEmpty())
                @php $champion = $podium[0]; @endphp
                <div class="lg:col-span-2 hidden sm:block">
                    <div class="relative mx-auto max-w-sm rounded-3xl bg-white/10 border border-white/15 backdrop-blur-md p-6 shadow-2xl">
                        <div class="absolute -top-5 left-1/2 -translate-x-1/2 flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-cta-400 text-zinc-900 text-xs font-extrabold shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.735H5.81a1 1 0 0 1-.957-.735L2.02 6.02a.5.5 0 0 1 .798-.52l4.276 3.664a1 1 0 0 0 1.516-.294z M5 21h14"/>
                            </svg>
                            Quán quân hiện tại
                        </div>

                        {{-- Ảnh quán quân --}}
                        <div class="mt-4 h-36 rounded-2xl overflow-hidden bg-primary-950/60 ring-1 ring-white/20">
                            @if($champion->cover_image)
                                <img src="{{ asset('storage/' . $champion->cover_image) }}" alt="{{ $champion->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white/40">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <h3 class="mt-4 text-lg font-extrabold text-white line-clamp-1">{{ $champion->name }}</h3>
                        <p class="text-xs text-white/60 mt-1 line-clamp-1">{{ $champion->district }}, {{ $champion->city }}</p>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-sm font-bold text-cta-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                                </svg>
                                {{ number_format($champion->bookings_count) }} lượt đặt
                            </span>
                            <span class="flex items-center gap-1 text-sm font-bold text-white">
                                <svg class="w-4 h-4 text-cta-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format((float) $champion->rating_avg, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════ BỘ LỌC MÔN THỂ THAO ═══════════════ --}}
<section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="-mt-8 sm:-mt-10 mb-10 card-base p-4 sm:p-5 shadow-lc-lg flex flex-col sm:flex-row sm:items-center gap-3">
        <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 shrink-0 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
            </svg>
            Xem theo môn:
        </span>
        <div class="flex gap-2 overflow-x-auto scrollbar-thin pb-1">
            <a href="{{ route('venues.popular') }}"
               class="shrink-0 px-3.5 py-1.5 rounded-full text-sm font-semibold transition-colors {{ $activeSport ? 'bg-tint-sky dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-tint-aqua' : 'bg-primary-600 text-white shadow-lc' }}">
                Tất cả
            </a>
            @foreach($sports as $sport)
                @php $isActive = $activeSport?->id === $sport->id; @endphp
                <a href="{{ route('venues.popular', ['sport_id' => $sport->id]) }}"
                   class="shrink-0 px-3.5 py-1.5 rounded-full text-sm font-semibold transition-colors {{ $isActive ? 'bg-primary-600 text-white shadow-lc' : 'bg-tint-sky dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-tint-aqua hover:text-primary-700 dark:hover:text-primary-300' }}">
                    {{ $sport->name }}
                    <span class="ml-1 text-xs {{ $isActive ? 'text-white/70' : 'text-zinc-400' }}">{{ $sport->courts_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ PODIUM TOP 3 ═══════════════ --}}
@if($podium->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="label-eyebrow mb-2">Bục vinh danh</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-50">Top 3 hottest</h2>
            </div>
            <span class="hidden sm:flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <svg class="w-3.5 h-3.5 text-accent-moss" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Cập nhật từ đơn đặt thật
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-end">
            @foreach($podium as $key => $venue)
                @php
                    $rank = $key + 1;
                    $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price');
                    $sportNames = $venue->courts->pluck('sport.name')->filter()->unique()->take(3);
                    $hotPercent = min(100, (int) round($venue->bookings_count / $maxBookings * 100));
                    $rankChip = [
                        1 => 'bg-cta-400 text-zinc-900 shadow-lg',
                        2 => 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-600',
                        3 => 'bg-accent-brown text-white shadow-lg',
                    ][$rank];
                @endphp

                <a href="{{ route('venues.show', $venue->slug) }}"
                   class="group relative bg-white dark:bg-zinc-900 rounded-3xl border {{ $rank === 1 ? 'border-cta-400/70 ring-4 ring-cta-400/20' : 'border-zinc-200 dark:border-zinc-800' }} shadow-lc-lg hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col
                          {{ $rank === 1 ? 'lg:order-2 lg:scale-[1.04] lg:z-10' : ($rank === 2 ? 'lg:order-1' : 'lg:order-3') }}">

                    {{-- Chip huy chương --}}
                    <span class="absolute top-4 left-4 z-10 flex items-center gap-1.5 w-11 h-11 rounded-full text-sm font-extrabold justify-center backdrop-blur-md {{ $rankChip }}">
                        @if($rank === 1)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.735H5.81a1 1 0 0 1-.957-.735L2.02 6.02a.5.5 0 0 1 .798-.52l4.276 3.664a1 1 0 0 0 1.516-.294z M5 21h14"/>
                            </svg>
                        @else
                            {{ $rank }}
                        @endif
                    </span>

                    {{-- Ảnh --}}
                    <div class="relative h-52 {{ $rank === 1 ? 'lg:h-60' : '' }} overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        @if($venue->cover_image)
                            <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-700 dark:to-zinc-800">
                                <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                </svg>
                                <span class="text-xs">Chưa có ảnh</span>
                            </div>
                        @endif
                        <span class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-white/20">
                            {{ $venue->courts_count }} sân
                        </span>
                    </div>

                    {{-- Nội dung --}}
                    <div class="p-5 flex-1 flex flex-col gap-3">
                        <div>
                            <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $venue->name }}
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="line-clamp-1">{{ $venue->address }}, {{ $venue->district }}, {{ $venue->city }}</span>
                            </p>
                        </div>

                        {{-- Tags môn thể thao --}}
                        @if($sportNames->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($sportNames as $name)
                                    <span class="text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 border border-primary-200 dark:border-primary-800 px-2 py-0.5 rounded-md">{{ $name }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Thanh độ hot --}}
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-primary-600 dark:text-primary-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                                    </svg>
                                    {{ number_format($venue->bookings_count) }} lượt đặt
                                </span>
                                <span class="font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-cta-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format((float) $venue->rating_avg, 1) }}
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-cta-400 transition-all duration-700" style="width: {{ $hotPercent }}%"></div>
                            </div>
                        </div>

                        {{-- Giá + CTA --}}
                        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between gap-3">
                            <div class="text-sm font-extrabold text-primary-600 dark:text-primary-400">
                                @if($minPrice)
                                    <span class="text-[11px] text-zinc-400 dark:text-zinc-500 font-medium">Từ </span>{{ number_format($minPrice, 0, ',', '.') }}đ
                                @else
                                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Liên hệ</span>
                                @endif
                            </div>
                            <span class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl text-xs font-bold {{ $rank === 1 ? 'bg-cta-400 hover:bg-cta-600 text-zinc-900' : 'bg-primary-600 hover:bg-primary-700 text-white' }} transition-colors">
                                Xem khu sân
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- ═══════════════ GRID HẠNG TIẾP THEO ═══════════════ --}}
@if($rest->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="label-eyebrow mb-2">Tiếp theo</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-50">Cũng rất được ưa chuộng</h2>
            </div>
            @if($activeSport)
                <span class="text-xs bg-tint-aqua dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800 px-2.5 py-1 rounded-full font-semibold">
                    {{ $activeSport->name }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($rest as $key => $venue)
                @php
                    $rank = $key + 4;
                    $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price');
                    $sportNames = $venue->courts->pluck('sport.name')->filter()->unique()->take(3);
                @endphp
                <a href="{{ route('venues.show', $venue->slug) }}"
                   class="group card-base hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">

                    <div class="relative h-44 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        @if($venue->cover_image)
                            <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-700 dark:to-zinc-800">
                                <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                </svg>
                                <span class="text-xs">Chưa có ảnh</span>
                            </div>
                        @endif

                        {{-- Chip hạng --}}
                        <span class="absolute top-3 left-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/95 dark:bg-zinc-900/90 backdrop-blur-md text-sm font-extrabold text-primary-600 dark:text-primary-400 border border-zinc-200 dark:border-zinc-700 shadow-sm">
                            {{ $rank }}
                        </span>
                        <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-white/20">
                            {{ $venue->courts_count }} sân
                        </span>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors mb-1">
                                {{ $venue->name }}
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3 flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="line-clamp-1">{{ $venue->address }}, {{ $venue->district }}, {{ $venue->city }}</span>
                            </p>
                            @if($sportNames->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($sportNames as $name)
                                        <span class="text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 border border-primary-200 dark:border-primary-800 px-2 py-0.5 rounded-md">{{ $name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                                </svg>
                                {{ number_format($venue->bookings_count) }} lượt đặt
                                <span class="text-zinc-300 dark:text-zinc-600">·</span>
                                <svg class="w-3.5 h-3.5 text-cta-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format((float) $venue->rating_avg, 1) }}
                            </span>
                            @if($minPrice)
                                <span class="text-sm font-extrabold text-primary-600 dark:text-primary-400">
                                    <span class="text-[11px] text-zinc-400 dark:text-zinc-500 font-medium">Từ </span>{{ number_format($minPrice, 0, ',', '.') }}đ
                                </span>
                            @else
                                <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Liên hệ</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- ═══════════════ TRỐNG / CTA CUỐI ═══════════════ --}}
@if($podium->isEmpty() && $rest->isEmpty())
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
        <div class="card-base p-8">
            <div class="flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 rounded-2xl bg-tint-sky dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Chưa có khu sân nào trên bảng xếp hạng</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6 max-w-sm">Bảng xếp hạng sẽ sớm có mặt khi các khu sân bắt đầu nhận đơn. Bạn có thể xem toàn bộ sân đang mở bán ngay bây giờ.</p>
                @if($activeSport)
                    <a href="{{ route('venues.popular') }}" class="btn-secondary mb-2 text-sm">Xóa bộ lọc {{ $activeSport->name }}</a>
                @endif
                <a href="{{ route('search') }}" class="btn-cta">Khám phá tất cả sân</a>
            </div>
        </div>
    </section>
@else
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 shadow-lc-lg px-6 py-10 sm:px-10 sm:py-12 text-center">
            <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 rounded-full bg-cta-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-10 w-64 h-64 rounded-full bg-cta-300/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Chưa tìm được sân ưng ý?</h2>
                <p class="mt-2 text-white/70 max-w-xl mx-auto">Dùng bộ lọc theo thành phố, môn thể thao và mức giá để tìm chính xác khu sân phù hợp với bạn nhất.</p>
                <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('search') }}" class="btn-cta">Tìm sân với bộ lọc</a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white border border-white/30 hover:bg-white/10 transition-colors">Liên hệ hỗ trợ</a>
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
