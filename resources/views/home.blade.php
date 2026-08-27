@extends('layouts.customer')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Hero Banner --}}
        <section class="relative bg-gradient-to-r from-green-700 via-green-600 to-teal-700 rounded-3xl shadow-xl overflow-hidden mb-12 text-white py-14 px-6 sm:px-12">
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
                    Tìm kiếm sân thể thao hoàn hảo của bạn 🏟️
                </h1>
                <p class="text-base sm:text-lg text-green-100 mb-8 font-medium">
                    Đặt chỗ dễ dàng, nhanh chóng và trải nghiệm các sân đấu chất lượng hàng đầu.
                </p>

                <form action="/search" method="GET" class="flex flex-col sm:flex-row gap-3 justify-center items-center max-w-2xl mx-auto bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20">
                    <div class="w-full sm:flex-1 relative">
                        <input type="text" name="q" placeholder="Tìm tên sân, địa điểm, quận..."
                               class="w-full pl-10 pr-4 py-3 rounded-xl text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 border-0">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <select name="sport_id" class="w-full sm:w-48 px-4 py-3 rounded-xl text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 border-0">
                        <option value="">Tất cả môn thể thao</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-green-500 hover:bg-green-400 text-gray-900 font-bold text-sm rounded-xl shadow-lg transition duration-200">
                        Tìm kiếm
                    </button>
                </form>
            </div>

            {{-- Trang trí hình nền --}}
            <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>
        </section>

        {{-- Sports Categories --}}
        <section class="mb-14">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                        Các môn thể thao phổ biến 🏆
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lựa chọn môn thể thao bạn muốn tham gia trải nghiệm</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                @foreach($sports as $sport)
                    <a href="/search?sport_id={{ $sport->id }}"
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 p-5 text-center flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-1">
                        @if ($sport->icon && \Storage::disk('public')->exists($sport->icon))
                            <img src="{{ asset('storage/' . $sport->icon) }}" alt="{{ $sport->name }}"
                                 class="w-16 h-16 object-cover rounded-full mb-3 group-hover:scale-110 transition-transform duration-300">
                        @else
                            <span class="text-4xl mb-3 group-hover:scale-110 transition-transform duration-300">🏅</span>
                        @endif
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                            {{ $sport->name }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Featured Venues --}}
        <section class="mb-12">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                        Khu sân nổi bật ⭐
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Những địa điểm đặt sân được ưa chuộng nhất</p>
                </div>
                <a href="/search" class="text-sm font-semibold text-green-600 dark:text-green-400 hover:text-green-700 flex items-center gap-1">
                    Xem tất cả
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($featuredVenues->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($featuredVenues as $venue)
                        <a href="/venues/{{ $venue->slug }}"
                           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 flex flex-col hover:-translate-y-1">
                            {{-- Image --}}
                            <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @if($venue->cover_image)
                                    <img src="{{ asset('storage/' . $venue->cover_image) }}" alt="{{ $venue->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                                        <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs">Chưa có ảnh</span>
                                    </div>
                                @endif

                                @php $courtCount = $venue->courts->where('status','active')->count(); @endphp
                                @if($courtCount > 0)
                                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-white/20">
                                        {{ $courtCount }} sân
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors line-clamp-1 mb-1">
                                        {{ $venue->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-start gap-1">
                                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-1">{{ $venue->address }}, {{ $venue->district }}, {{ $venue->city }}</span>
                                    </p>

                                    {{-- Sports Pills --}}
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

                                {{-- Footer: Rating & Price --}}
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
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
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Hiện chưa có khu sân nổi bật nào được cập nhật.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
