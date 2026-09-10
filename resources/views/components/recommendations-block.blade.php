@props([
    'courtId' => null,
    'venueId' => null,
    'title'   => 'Có thể bạn cũng thích',
    'subtitle'=> 'Gợi ý từ thói quen đặt sân của cộng đồng người chơi thể thao',
    'limit'   => 4,
])

@php
    $apriori = app(\App\Services\Recommendation\AprioriService::class);
    $recommendations = collect();

    if ($courtId) {
        $recommendations = $apriori->getRecommendationsForCourt((int)$courtId, (int)$limit);
    } elseif ($venueId) {
        $recommendations = $apriori->getRecommendationsForVenue((int)$venueId, (int)$limit);
    }
@endphp

@if($recommendations->isNotEmpty())
<div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-800">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-2">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-800/60 mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>AI Association Rules</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-50 tracking-tight flex items-center gap-2">
                {{ $title }}
            </h2>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $subtitle }}
            </p>
        </div>
        <span class="text-xs text-zinc-400 dark:text-zinc-500 self-start sm:self-auto flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Tự động phân tích theo đơn hàng
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($recommendations as $court)
            @php
                $meta = $court->recommendation_meta ?? [];
                $minPrice = $court->slots->min('price') ?? $court->price_snapshot ?? 0;
                $imageUrl = $court->image ? asset('storage/' . $court->image) : asset('images/defaults/court.jpg');
                $isFrequentPair = ($meta['badge'] ?? '') === 'Thường đặt cùng';
            @endphp
            <div class="group flex flex-col bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-primary-500/50 transition-all duration-300 overflow-hidden">
                {{-- Image & Badge --}}
                <div class="relative aspect-video w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $imageUrl }}" alt="{{ $court->name }}"
                         onerror="this.src='{{ asset('images/defaults/court.jpg') }}'"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    {{-- Badge lý do gợi ý --}}
                    <div class="absolute top-2.5 left-2.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold backdrop-blur-md {{ $isFrequentPair ? 'bg-primary-600/90 text-white shadow-sm' : 'bg-zinc-900/80 text-zinc-100 border border-white/10' }}">
                            @if($isFrequentPair)
                                <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                            {{ $meta['badge'] ?? 'Gợi ý cho bạn' }}
                        </span>
                    </div>

                    {{-- Tag môn thể thao --}}
                    @if($court->sport)
                        <div class="absolute bottom-2.5 right-2.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-black/60 text-white backdrop-blur-sm">
                                {{ $court->sport->name }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Card Content --}}
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <a href="{{ route('customer.bookings.create', $court->id) }}" class="block group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 line-clamp-1 text-base">
                                {{ $court->name }}
                            </h3>
                        </a>

                        @if($court->venue)
                            <a href="{{ route('venues.show', $court->venue->slug) }}" class="text-xs text-zinc-500 dark:text-zinc-400 hover:underline line-clamp-1 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span>{{ $court->venue->name }}</span>
                            </a>
                        @endif

                        <p class="text-xs text-primary-600 dark:text-primary-400 mt-2 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500 shrink-0"></span>
                            {{ $meta['reason'] ?? 'Phù hợp với sở thích của bạn' }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[11px] text-zinc-400 uppercase tracking-wider font-medium block">Giá từ</span>
                            <span class="text-sm font-extrabold text-primary-600 dark:text-primary-400">
                                {{ number_format($minPrice, 0, ',', '.') }} đ
                                <span class="text-[11px] font-normal text-zinc-400">/h</span>
                            </span>
                        </div>

                        <a href="{{ route('customer.bookings.create', $court->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500">
                            Đặt ngay
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
