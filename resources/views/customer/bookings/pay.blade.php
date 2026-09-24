@extends('layouts.customer')

@section('title', 'Thanh toán ' . $booking->code)

@section('content')
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg mx-auto card-base p-6" x-data="payPoll({{ $booking->id }}, {{ $booking->payment_status === 'unpaid' && !$booking->isCancelled() ? 'true' : 'false' }})">

            <h2 class="text-2xl font-bold mb-1 text-zinc-900 dark:text-zinc-100">Thanh toán chuyển khoản</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                Mã đơn: <span class="font-bold text-primary-600 dark:text-primary-400">{{ $booking->code }}</span>
            </p>

            {{-- Tóm tắt đơn --}}
            <div class="mb-6 text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Sân</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $booking->court->name ?? 'N/A' }} — {{ $booking->court->venue->name ?? '' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Thời gian</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }},
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Tổng tiền</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">
                        @if($booking->discount_amount > 0)
                            <span class="line-through text-zinc-400 dark:text-zinc-500 mr-1.5">{{ number_format($booking->total_amount + $booking->discount_amount) }}</span>
                        @endif
                        {{ number_format($booking->total_amount) }} VNĐ
                    </span>
                </div>
                @if($booking->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-emerald-600 dark:text-emerald-400">Mã {{ $booking->promotion?->code ?? '' }}</span>
                        <span class="font-medium text-emerald-600 dark:text-emerald-400">-{{ number_format($booking->discount_amount) }} VNĐ</span>
                    </div>
                @endif
                @if($booking->deposit_amount)
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Cọc trước</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($booking->deposit_amount) }} VNĐ</span>
                    </div>
                @endif
            </div>

            @if($booking->payment_status !== 'unpaid')
                {{-- Đã nhận tiền --}}
                <div class="p-4 mb-4 text-sm bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl">
                    <p class="font-semibold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Đã nhận thanh toán ({{ $booking->payment_status === 'fully_paid' ? 'đủ tiền' : 'đã cọc' }})
                    </p>
                    <p>Đơn của bạn {{ $booking->status === 'confirmed' ? 'đã được xác nhận.' : 'đang chờ chủ sân xác nhận.' }}</p>
                </div>
                <a href="{{ route('customer.bookings.index') }}"
                   class="block w-full text-center btn-primary">
                    Xem lịch sử đặt sân
                </a>
            @elseif($booking->isCancelled())
                {{-- Đơn đã hủy --}}
                <div class="p-4 mb-4 text-sm bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                    Đơn này đã bị hủy, không thể thanh toán.
                </div>
            @else
                {{-- Chờ thanh toán --}}
                <div class="border border-zinc-200 dark:border-zinc-700 bg-tint-sky/50 dark:bg-transparent rounded-xl p-6 text-center">
                    <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
                        Quét QR bằng app ngân hàng
                        @if($booking->deposit_amount)
                            (chuyển đủ cọc <b>{{ number_format($booking->deposit_amount) }}</b> hoặc đủ tổng)
                        @endif
                    </p>

                    {{-- Đếm ngược hạn thanh toán — hết giờ đơn bị hủy tự động (15 phút) --}}
                    <div class="mb-4 px-3 py-2 text-sm bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 rounded-xl flex items-center justify-center gap-2"
                         x-data="{
                            deadline: new Date('{{ $booking->paymentExpiresAt()->format('Y-m-d\TH:i:s') }}').getTime(),
                            left: 0, tick() { this.left = Math.max(0, this.deadline - Date.now()); },
                         }"
                         x-init="tick(); setInterval(() => tick(), 1000)"
                         x-cloak>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>
                            Đơn được giữ trong <b x-text="Math.floor(left / 60000) + ':' + String(Math.floor(left / 1000) % 60).padStart(2, '0')"></b>
                            — hết {{ \App\Models\Booking::PAYMENT_EXPIRY_MINUTES }} phút không thanh toán sẽ tự hủy.
                        </span>
                    </div>

                    <img src="{{ $qrUrl }}" alt="Mã QR VietQR" class="w-64 mx-auto rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white p-2">

                    <div class="mt-4 text-sm text-left space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-zinc-500 dark:text-zinc-400">Số tiền</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($amount) }} VNĐ</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-zinc-500 dark:text-zinc-400">Nội dung CK</span>
                            <span class="font-mono font-bold text-primary-600 dark:text-primary-400">{{ $booking->code }}</span>
                            <button type="button" @click="navigator.clipboard.writeText('{{ $booking->code }}')"
                                    class="text-xs bg-zinc-100 dark:bg-zinc-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-zinc-600 dark:text-zinc-300 px-2 py-1 rounded-lg transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-center text-xs text-zinc-400 dark:text-zinc-500 animate-pulse" x-cloak>
                    Đang chờ chuyển khoản... Trang sẽ tự cập nhật khi nhận được tiền.
                </p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Poll trạng thái thanh toán — reload khi webhook đã cập nhật đơn
    // Chỉ poll khi trang đang chờ tiền (unpaid + chưa hủy) — trang đã hiện
    // kết quả (đã nhận tiền / đã hủy) thì poll sẽ reload vô hạn vì điều kiện
    // vẫn đúng mãi.
    function payPoll(id, shouldPoll) {
        return {
            init() {
                if (!shouldPoll) return;

                setInterval(async () => {
                    try {
                        const res = await fetch(`{{ url('bookings') }}/${id}/status`);
                        const data = await res.json();
                        if (data.payment_status !== 'unpaid' || data.status === 'cancelled') {
                            window.location.reload();
                        }
                    } catch (e) { /* bỏ qua lỗi mạng tạm thời */ }
                }, 4000);
            },
        };
    }
</script>
@endpush
