@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header tiêu đề trang --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                Tìm sân thể thao 🏟️
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Tìm kiếm và đặt sân thể thao nhanh chóng, dễ dàng tại khu vực của bạn.
            </p>
        </div>

        @if(request()->hasAny(['q', 'sport_id', 'city', 'sort']))
            <a href="/search" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:text-red-700 bg-red-50 dark:bg-red-900/30 px-3 py-2 rounded-lg border border-red-200 dark:border-red-800 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Xóa bộ lọc tìm kiếm
            </a>
        @endif
    </div>

    {{-- Bố cục Grid 12 cột: 3 cột Sidebar - 9 cột Kết quả --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- ── SIDEBAR BỘ LỌC (3/12 cột = 25% width) ── --}}
        <aside class="lg:col-span-3">
            <form action="/search" method="GET" id="filter-form">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-5 sticky top-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Bộ lọc tìm kiếm
                        </h2>
                    </div>

                    {{-- 1. Tìm theo từ khóa --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🔍 Từ khóa
                        </label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Tên sân, địa chỉ, quận..."
                                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- 2. Lọc theo Môn thể thao --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🏅 Môn thể thao
                        </label>
                        <select name="sport_id"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="">Tất cả môn thể thao</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Lọc theo Thành phố --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            📍 Thành phố
                        </label>
                        <select name="city"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="">Tất cả thành phố</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Sắp xếp --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            🔃 Sắp xếp theo
                        </label>
                        <select name="sort"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                            <option value="name"   {{ request('sort') == 'name'   ? 'selected' : '' }}>Tên A → Z</option>
                        </select>
                    </div>

                    {{-- Nút Áp dụng --}}
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-xl shadow-sm hover:shadow-md transition duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Áp dụng bộ lọc
                        </button>

                        @if(request()->hasAny(['q', 'sport_id', 'city', 'sort']))
                            <a href="/search"
                               class="mt-2 block w-full text-center text-xs font-medium text-red-500 hover:text-red-700 py-1.5 transition">
                                ✕ Xóa bộ lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </aside>

        {{-- ── DANH SÁCH KẾT QUẢ SÂN (9/12 cột = 75% width) ── --}}
        <main class="lg:col-span-9">
            {{-- Thanh thông tin kết quả tìm kiếm --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 px-5 py-4 mb-6 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-bold text-gray-900 dark:text-gray-100 text-base">
                        {{ $venues->total() }}
                    </span>
                    <span>khu sân thể thao được tìm thấy</span>
                    @if(request('q'))
                        <span class="text-gray-400">|</span>
                        <span>Từ khóa: <strong class="text-green-600 dark:text-green-400">"{{ request('q') }}"</strong></span>
                    @endif
                </div>

                {{-- Badges bộ lọc đang active --}}
                <div class="hidden sm:flex items-center gap-2">
                    @if(request('sport_id'))
                        @php $selectedSport = $sports->firstWhere('id', request('sport_id')); @endphp
                        @if($selectedSport)
                            <span class="inline-flex items-center gap-1 text-xs bg-green-50 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-2.5 py-1 rounded-full border border-green-200 dark:border-green-800">
                                🏅 {{ $selectedSport->name }}
                            </span>
                        @endif
                    @endif
                    @if(request('city'))
                        <span class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2.5 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                            📍 {{ request('city') }}
                        </span>
                    @endif
                </div>
            </div>

            @if($venues->count() > 0)
                {{-- Grid thẻ sân: 3 cột trên màn hình desktop --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($venues as $venue)
                        <a href="/venues/{{ $venue->slug }}"
                           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 flex flex-col hover:-translate-y-1">

                            {{-- Image container --}}
                            <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @if($venue->cover_image)
                                    <img src="{{ asset('storage/' . $venue->cover_image) }}"
                                         alt="{{ $venue->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                                        <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs">Chưa có ảnh</span>
                                    </div>
                                @endif

                                {{-- Badge số sân con --}}
                                @php $courtCount = $venue->courts->where('status','active')->count(); @endphp
                                @if($courtCount > 0)
                                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-white/20">
                                        {{ $courtCount }} sân
                                    </span>
                                @endif
                            </div>

                            {{-- Card body --}}
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    {{-- Tên khu sân --}}
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors line-clamp-1 mb-1">
                                        {{ $venue->name }}
                                    </h3>

                                    {{-- Địa chỉ --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-start gap-1">
                                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-1">{{ $venue->address }}, {{ $venue->district }}, {{ $venue->city }}</span>
                                    </p>

                                    {{-- Danh sách môn --}}
                                    @php
                                        $sportNames = $venue->courts->pluck('sport.name')->filter()->unique()->take(3);
                                    @endphp
                                    @if($sportNames->count() > 0)
                                        <div class="flex flex-wrap gap-1.5 mb-4">
                                            @foreach($sportNames as $sportName)
                                                <span class="text-[11px] font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 px-2 py-0.5 rounded-md">
                                                    {{ $sportName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer của Card: Đánh giá & Giá --}}
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    {{-- Đánh giá --}}
                                    <div class="flex items-center gap-1">
                                        @php $rating = round($venue->rating_avg ?? 0); @endphp
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ number_format($venue->rating_avg ?? 0, 1) }}
                                        </span>
                                    </div>

                                    {{-- Giá --}}
                                    @php
                                        $minPrice = $venue->courts->flatMap(fn($c) => $c->slots)->min('price');
                                    @endphp
                                    <div class="text-right">
                                        @if($minPrice)
                                            <span class="text-xs text-gray-400 block">Từ</span>
                                            <span class="text-sm font-extrabold text-green-600 dark:text-green-400">
                                                {{ number_format($minPrice, 0, ',', '.') }}đ
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-gray-400">Liên hệ</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Phân trang --}}
                <div class="mt-8">
                    {{ $venues->links() }}
                </div>

            @else
                {{-- Trạng thái rỗng --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center text-4xl mb-4">
                        🏟️
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Không tìm thấy khu sân nào phù hợp
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mb-6">
                        Rất tiếc, không tìm thấy khu sân nào khớp với các tiêu chí tìm kiếm của bạn. Hãy thử thay đổi từ khóa hoặc xóa bớt bộ lọc.
                    </p>
                    <a href="/search"
                       class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition shadow-sm hover:shadow-md">
                        Xem tất cả các sân
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
