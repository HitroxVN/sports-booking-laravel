@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="/" class="hover:text-green-600">Trang chủ</a>
        <span>/</span>
        <a href="/search" class="hover:text-green-600">Tìm sân</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ $venue->name }}</span>
    </nav>

    {{-- ── 1. KHUNG THÔNG TIN TỔNG QUAN KHU SÂN ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-10">
        {{-- Banner & Cover Image --}}
        <div class="relative h-64 sm:h-80 w-full bg-gray-900">
            @if($venue->cover_image)
                <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}" class="w-full h-full object-cover opacity-90">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gradient-to-r from-gray-800 to-gray-900">
                    <svg class="w-20 h-20 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-sm font-medium">Chưa có ảnh bìa khu sân</span>
                </div>
            @endif

            {{-- Badges nổi đè trên banner --}}
            <div class="absolute bottom-4 left-6 flex flex-wrap gap-2">
                <span class="bg-green-600/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                    ✓ Đã xác minh & Duyệt
                </span>
                <span class="bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">
                    {{ $venue->courts->count() }} sân con
                </span>
            </div>
        </div>

        {{-- Chi tiết thông tin khu sân --}}
        <div class="p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div class="space-y-3">
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                        {{ $venue->name }}
                    </h1>

                    <p class="text-sm text-gray-600 dark:text-gray-300 flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $venue->address }}, {{ $venue->district }}, {{ $venue->city }}</span>
                    </p>

                    @if($venue->phone)
                        <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>Hotline: <strong class="text-gray-800 dark:text-gray-200">{{ $venue->phone }}</strong></span>
                        </p>
                    @endif
                </div>

                {{-- Đánh giá & Giá khởi điểm --}}
                <div class="flex sm:flex-col items-end justify-between sm:justify-start gap-4">
                    <div class="flex items-center gap-2 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 px-4 py-2 rounded-2xl">
                        <div class="flex text-yellow-400">
                            @php $rating = round($venue->rating_avg ?? 0); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-base font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format($venue->rating_avg ?? 0, 1) }}
                        </span>
                        <span class="text-xs text-gray-500">({{ $venue->reviews->count() }} đánh giá)</span>
                    </div>

                    @php
                        $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price');
                    @endphp
                    @if($minPrice)
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">Mức giá thuê từ</span>
                            <span class="text-2xl font-extrabold text-green-600 dark:text-green-400">
                                {{ number_format($minPrice, 0, ',', '.') }}đ <span class="text-xs font-normal text-gray-500">/giờ</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mô tả & Tiện ích --}}
            @if($venue->description)
                <div class="mt-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-2">Giới thiệu khu sân</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ $venue->description }}
                    </p>
                </div>
            @endif

            @if(is_array($venue->amenities) && count($venue->amenities) > 0)
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Tiện ích sân</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($venue->amenities as $amenity)
                            <span class="inline-flex items-center gap-1.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600">
                                ✨ {{ $amenity }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>


    {{-- ── 2. BỘ LỌC VÀ DANH SÁCH SÂN CON (CSS Grid 12 cột) ── --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                Danh sách sân con 🏟️
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Lọc và chọn sân phù hợp nhất cho lịch đấu của bạn
            </p>
        </div>

        @if(request()->hasAny(['q', 'sport_id', 'surface_type', 'status', 'sort']))
            <a href="{{ route('venues.show', $venue->slug) }}"
               class="text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 dark:bg-red-900/30 px-3 py-2 rounded-xl border border-red-200 dark:border-red-800 transition">
                ✕ Xóa bộ lọc sân con
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- ── SIDEBAR BỘ LỌC SÂN CON (3/12 cột = 25% width) ── --}}
        <aside class="lg:col-span-3">
            <form action="{{ route('venues.show', $venue->slug) }}" method="GET" id="court-filter-form">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-5 sticky top-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Lọc sân con
                        </h3>
                    </div>

                    {{-- 1. Tìm tên sân --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🔍 Tên sân
                        </label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="VD: Sân A1, Sân VIP..."
                               class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 transition">
                    </div>

                    {{-- 2. Môn thể thao --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🏅 Môn thể thao
                        </label>
                        <select name="sport_id"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 transition">
                            <option value="">Tất cả các môn</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Loại mặt sân --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🌱 Loại mặt sân
                        </label>
                        <select name="surface_type"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 transition">
                            <option value="">Tất cả mặt sân</option>
                            <option value="artificial_turf" {{ request('surface_type') == 'artificial_turf' ? 'selected' : '' }}>Cỏ nhân tạo</option>
                            <option value="natural_grass"   {{ request('surface_type') == 'natural_grass'   ? 'selected' : '' }}>Cỏ tự nhiên</option>
                            <option value="wood"            {{ request('surface_type') == 'wood'            ? 'selected' : '' }}>Sàn gỗ</option>
                            <option value="concrete"        {{ request('surface_type') == 'concrete'        ? 'selected' : '' }}>Bê tông</option>
                        </select>
                    </div>

                    {{-- 4. Sắp xếp theo --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🔃 Sắp xếp theo
                        </label>
                        <select name="sort"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 transition">
                            <option value="name"       {{ request('sort', 'name') == 'name'       ? 'selected' : '' }}>Tên sân A → Z</option>
                            <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                            <option value="latest"     {{ request('sort') == 'latest'     ? 'selected' : '' }}>Mới nhất</option>
                        </select>
                    </div>

                    {{-- Nút áp dụng --}}
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-xl shadow-sm hover:shadow-md transition">
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </form>
        </aside>

        {{-- ── DANH SÁCH SÂN CON (9/12 cột = 75% width) ── --}}
        <main class="lg:col-span-9">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 px-5 py-4 mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Hiển thị <strong class="text-gray-900 dark:text-gray-100 font-bold text-base">{{ $courts->count() }}</strong> sân con thuộc {{ $venue->name }}
                </p>
            </div>

            @if($courts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($courts as $court)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-200 dark:border-gray-700 p-5 transition-all duration-300 flex flex-col justify-between hover:-translate-y-1">
                            <div>
                                {{-- Header thẻ sân con --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div>
                                        <span class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-2.5 py-0.5 rounded-full inline-block mb-1">
                                            {{ $court->sport->name ?? 'Môn thể thao' }}
                                        </span>
                                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg leading-snug">
                                            {{ $court->name }}
                                        </h3>
                                    </div>

                                    @if($court->status === 'active')
                                        <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-[11px] font-bold px-2.5 py-1 rounded-full">
                                            Hoạt động
                                        </span>
                                    @else
                                        <span class="bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 text-[11px] font-bold px-2.5 py-1 rounded-full">
                                            {{ $court->status_name }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Mô tả ngắn --}}
                                @if($court->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                        {{ $court->description }}
                                    </p>
                                @endif

                                {{-- Chi tiết thông số: Mặt sân, sức chứa --}}
                                <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300 mb-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Mặt sân:</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $court->surface_type_name }}</span>
                                    </div>
                                    @if($court->max_players)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400">Sức chứa:</span>
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">Tối đa {{ $court->max_players }} người</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer: Giá & Nút Đặt sân --}}
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                @php
                                    $minCourtPrice = $court->slots->min('price');
                                @endphp
                                <div>
                                    <span class="text-[11px] text-gray-400 block">Giá thuê từ</span>
                                    @if($minCourtPrice)
                                        <span class="text-base font-extrabold text-green-600 dark:text-green-400">
                                            {{ number_format($minCourtPrice, 0, ',', '.') }}đ <span class="text-[11px] font-normal text-gray-400">/giờ</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Liên hệ</span>
                                    @endif
                                </div>

                                <a href="/search?q={{ urlencode($court->name) }}"
                                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl transition shadow-sm hover:shadow-md">
                                    Đặt sân này
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center flex flex-col items-center justify-center">
                    <div class="text-5xl mb-3">🏟️</div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">
                        Không tìm thấy sân con nào phù hợp
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Hãy thử điều chỉnh bộ lọc hoặc chọn môn thể thao khác.
                    </p>
                    <a href="{{ route('venues.show', $venue->slug) }}"
                       class="px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-xl">
                        Xóa bộ lọc
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
