<x-app-layout>
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg mx-auto bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-2 text-gray-800">Thanh toán đơn sân</h2>
            <p class="text-sm text-gray-500 mb-6">
                Quét mã QR bằng ứng dụng ngân hàng để chuyển khoản.
                Vui lòng giữ đúng nội dung chuyển khoản để đối soát đơn hàng.
            </p>

            {{-- Thông tin đơn --}}
            <div class="rounded-lg bg-gray-50 p-4 mb-6 text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Mã đơn</span>
                    <span class="font-bold text-indigo-600">{{ $booking->code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Sân</span>
                    <span class="font-semibold text-gray-900">{{ $booking->court->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Thời gian</span>
                    <span class="font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2">
                    <span class="text-gray-500">Số tiền</span>
                    <span class="font-bold text-red-600 text-lg">
                        {{ number_format($booking->total_amount) }} VNĐ
                    </span>
                </div>
            </div>

            {{-- Mã QR --}}
            <div class="flex justify-center mb-6">
                <img
                    src="https://img.vietqr.io/image/{{ config('vietqr.bank_id') }}-{{ config('vietqr.account_no') }}-{{ config('vietqr.template') }}.png?amount={{ (int) $booking->total_amount }}&addInfo={{ urlencode($booking->code) }}&accountName={{ urlencode(config('vietqr.account_name')) }}"
                    alt="QR thanh toán"
                    class="w-64 h-64 rounded-lg border border-gray-200"
                >
            </div>

            {{-- Nội dung chuyển khoản --}}
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-3 mb-6 text-center">
                <p class="text-xs text-yellow-700 mb-1">Nội dung chuyển khoản</p>
                <p class="font-mono font-bold text-yellow-800">{{ $booking->code }}</p>
            </div>

            {{-- Nút hành động --}}
            <div class="flex gap-3">
                <a href="{{ route('customer.bookings.index') }}"
                   class="flex-1 text-center px-4 py-2.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-semibold">
                    Quay lại
                </a>
                <a href="{{ route('customer.bookings.index') }}"
                   class="flex-1 text-center px-4 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition text-sm font-semibold">
                    Đã chuyển khoản
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
