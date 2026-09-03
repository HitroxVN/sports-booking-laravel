@extends('layouts.customer')

@section('title', 'Thanh toán ' . $booking->code)

@section('content')
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg mx-auto card-base p-6" x-data="payPoll({{ $booking->id }})">

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
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($booking->total_amount) }} VNĐ</span>
                </div>
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
                    <p class="font-semibold mb-1">
                        ✓ Đã nhận thanh toán ({{ $booking->payment_status === 'fully_paid' ? 'đủ tiền' : 'đã cọc' }})
                    </p>
                    <p>Đơn của bạn {{ $booking->status === 'confirmed' ? 'đã được xác nhận.' : 'đang chờ chủ sân xác nhận.' }}</p>
                </div>
                <a href="{{ route('customer.bookings.index') }}"
                   class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-xl">
                    Xem lịch sử đặt sân
                </a>
            @elseif($booking->isCancelled())
                {{-- Đơn đã hủy --}}
                <div class="p-4 mb-4 text-sm bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                    Đơn này đã bị hủy, không thể thanh toán.
                </div>
            @else
                {{-- Chờ thanh toán --}}
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 text-center">
                    <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
                        Quét QR bằng app ngân hàng
                        @if($booking->deposit_amount)
                            (chuyển đủ cọc <b>{{ number_format($booking->deposit_amount) }}</b> hoặc đủ tổng)
                        @endif
                    </p>

                    <img src="{{ $qrUrl }}" alt="Mã QR VietQR" class="w-64 mx-auto rounded-xl border border-zinc-200 dark:border-zinc-700">

                    <div class="mt-4 text-sm text-left space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-zinc-500 dark:text-zinc-400">Số tiền</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($amount) }} VNĐ</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-zinc-500 dark:text-zinc-400">Nội dung CK</span>
                            <span class="font-mono font-bold text-primary-600 dark:text-primary-400">{{ $booking->code }}</span>
                            <button type="button" @click="navigator.clipboard.writeText('{{ $booking->code }}')"
                                    class="text-xs bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 px-2 py-1 rounded-lg">
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
    function payPoll(id) {
        return {
            init() {
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
