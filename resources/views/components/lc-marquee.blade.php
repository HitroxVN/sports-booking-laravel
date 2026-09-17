{{-- Marquee chân trang: dòng chữ tự chạy ngang, loop CSS translateX(-50%)
     trên 2 bản sao nội dung giống hệt nhau (không dùng thẻ <marquee> lỗi thời).
     Hover để tạm dừng, prefers-reduced-motion sẽ dừng hẳn (xem app.css). --}}
@php
    $marqueePhrases = [
        ['text' => 'Nơi đam mê hội tụ', 'hot' => false],
        ['text' => 'Nơi những trận đấu bắt đầu', 'hot' => false],
        ['text' => 'Đặt sân ngay hôm nay và sẵn sàng bùng cháy!', 'hot' => true],
    ];
@endphp
<div class="lc-marquee" role="marquee" aria-label="Khẩu hiệu Arena">
    <div class="lc-marquee__viewport">
        <div class="lc-marquee__track">
            @foreach([1, 2] as $copy)
                <div class="lc-marquee__group" @if($copy === 1) aria-hidden="false" @else aria-hidden="true" @endif>
                    @foreach($marqueePhrases as $phrase)
                        <span class="lc-marquee__item {{ $phrase['hot'] ? 'lc-marquee__item--hot' : '' }}">
                            @if($phrase['hot'])
                                <svg class="w-3.5 h-3.5 text-cta-400 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-cta-300/70 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 0l2.4 7.6L20 10l-7.6 2.4L10 20l-2.4-7.6L0 10l7.6-2.4z"/>
                                </svg>
                            @endif
                            {{ $phrase['text'] }}
                        </span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
