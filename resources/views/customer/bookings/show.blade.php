@extends('layouts.customer')

@section('title', 'Chi tiết đơn ' . $booking->code)

@section('content')
<div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        {{-- Nút quay lại --}}
        <a href="{{ route('customer.bookings.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Về lịch sử đặt sân
        </a>

        <div class="card-base p-6 sm:p-8">
            {{-- Header: mã đơn + trạng thái --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Chi tiết đơn đặt sân</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Mã đơn: <span class="font-mono font-bold text-primary-600 dark:text-primary-400">{{ $booking->code }}</span>
                    </p>
                </div>
                <div class="flex flex-col items-end gap-1.5">
                    <x-badge :variant="$booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'completed' ? 'info' : 'danger'))">
                        @if($booking->isPending()) Chờ xác nhận
                        @elseif($booking->isConfirmed()) Đã xác nhận
                        @elseif($booking->isCompleted()) Đã hoàn tất
                        @elseif($booking->isCancelled()) Đã hủy
                        @else {{ ucfirst($booking->status) }} @endif
                    </x-badge>
                    @if($booking->isPaid())
                        <span class="text-xs font-semibold text-green-600 dark:text-green-400">✓ Đã thanh toán đủ</span>
                    @elseif($booking->hasDeposit())
                        <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">✓ Đã đặt cọc</span>
                    @else
                        <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">Chưa thanh toán</span>
                    @endif
                </div>
            </div>

            {{-- Thông tin sân + thời gian --}}
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                    <p class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Sân đặt</p>
                    <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ $booking->court->name ?? 'N/A' }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $booking->court->venue->name ?? '' }}</p>
                    <a href="{{ $booking->court->venue ? route('venues.show', $booking->court->venue->slug) : '#' }}"
                       class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline mt-1 inline-block">
                        Xem khu sân →
                    </a>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                    <p class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Thời gian chơi</p>
                    <p class="font-bold text-zinc-900 dark:text-zinc-100">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('vi')->translatedFormat('l, d/m/Y') }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        ({{ $booking->duration }} phút)
                    </p>
                </div>
            </div>

            {{-- Chi tiết tiền --}}
            <div class="mb-6">
                <p class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Chi tiết thanh toán</p>
                <div class="text-sm space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Giá sân ({{ $booking->duration }} phút)</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ number_format($booking->total_amount + $booking->discount_amount) }} VNĐ</span>
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Mã giảm giá {{ $booking->promotion?->code ?? '' }}</span>
                            <span>-{{ number_format($booking->discount_amount) }} VNĐ</span>
                        </div>
                    @endif
                    @if($booking->deposit_amount)
                        <div class="flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Đặt cọc trước</span>
                            <span class="text-zinc-900 dark:text-zinc-100">{{ number_format($booking->deposit_amount) }} VNĐ</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800 font-bold text-base">
                        <span class="text-zinc-900 dark:text-zinc-100">Tổng tiền</span>
                        <span class="text-primary-600 dark:text-primary-400">{{ number_format($booking->total_amount) }} VNĐ</span>
                    </div>
                </div>
            </div>

            {{-- Lý do hủy (nếu bị hủy) --}}
            @if($booking->isCancelled() && $booking->cancel_reason)
                <div class="p-4 mb-6 text-sm bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                    <span class="font-semibold">Lý do hủy:</span> {{ $booking->cancel_reason }}
                </div>
            @endif

            {{-- Thanh toán chưa xong: tiếp tục thanh toán --}}
            @if($booking->isPending() && $booking->payment_status === 'unpaid' && !$booking->isPaymentExpired())
                <a href="{{ route('customer.bookings.pay', $booking) }}" class="btn-primary w-full text-center block mb-6">
                    Tiếp tục thanh toán
                </a>
            @endif

            {{-- ── ĐÁNH GIÁ ── --}}
            <div class="pt-5 border-t border-zinc-100 dark:border-zinc-800">
                @if($booking->isCompleted() && !$booking->review)
                    {{-- Chưa đánh giá: form chọn sao + bình luận --}}
                    <h3 class="font-bold text-zinc-900 dark:text-zinc-100 mb-3">Đánh giá khu sân</h3>
                    <form action="{{ route('customer.bookings.review', $booking) }}" method="POST"
                          x-data="{ rating: {{ old('rating', 0) }} }" class="space-y-3">
                        @csrf
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}" class="p-0.5" :aria-label="'{{ $i }} sao'">
                                    <svg class="w-7 h-7 transition-colors" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600'"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <span class="ml-2 text-sm text-zinc-500 dark:text-zinc-400" x-show="rating > 0" x-text="rating + '/5 sao'"></span>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        <textarea name="comment" rows="3" maxlength="1000"
                                  placeholder="Chia sẻ trải nghiệm của bạn về khu sân..."
                                  class="input-base">{{ old('comment') }}</textarea>
                        <button type="submit" class="btn-primary" :disabled="rating === 0">Gửi đánh giá</button>
                    </form>
                @elseif($booking->review)
                    {{-- Đã đánh giá: hiển thị review + phản hồi của chủ sân (nếu có) --}}
                    <h3 class="font-bold text-zinc-900 dark:text-zinc-100 mb-3">Đánh giá của bạn</h3>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                        <div class="flex items-center gap-1 mb-1.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($booking->review->comment)
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $booking->review->comment }}</p>
                        @endif
                    </div>
                    @if($booking->review->owner_reply)
                        <div class="ml-6 mt-2 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4">
                            <p class="text-xs font-bold text-primary-600 dark:text-primary-400 mb-1">Phản hồi từ khu sân</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $booking->review->owner_reply }}</p>
                        </div>
                    @endif
                @elseif($booking->isCompleted())
                    @php /* review bị ẩn/soft-delete nhưng đơn vẫn completed */ @endphp
                @elseif($booking->isConfirmed() || $booking->isPending())
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">
                        Có thể đánh giá sau khi chơi xong (đơn chuyển sang "Đã hoàn tất").
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
