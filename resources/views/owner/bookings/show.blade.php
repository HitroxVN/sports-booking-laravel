<x-owner-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.bookings.index') }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Chi Tiết Đơn Đặt Sân: <span class="text-primary-600 dark:text-primary-400">#{{ $booking->code }}</span>
            </h1>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Cột trái: Thông tin chi tiết -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Khối thông tin khách & sân -->
                <div class="card-base p-8">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Thông Tin Đơn Đặt</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="label-eyebrow mb-3">Khách Hàng</h4>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">{{ $booking->user->name ?? 'Khách Vãng Lai' }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-1">{{ $booking->user->phone ?? 'Không có SĐT' }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ $booking->user->email ?? 'Không có Email' }}</p>
                        </div>
                        <div>
                            <h4 class="label-eyebrow mb-3">Sân Thể Thao</h4>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">{{ $booking->court->venue->name }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-1">Sân: <span class="font-semibold">{{ $booking->court->name }}</span></p>
                            <!-- Đã fix Lỗi 11: Gọi hàm surface_type_name -->
                            <p class="text-zinc-600 dark:text-zinc-400">Loại: {{ $booking->court->surface_type_name }}</p>
                        </div>
                    </div>

                    <div class="mt-8 bg-zinc-50 dark:bg-zinc-800/50 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <h4 class="label-eyebrow mb-4">Thời Gian & Giá Trị</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Ngày đá</p>
                                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Khung giờ</p>
                                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Đã cọc</p>
                                <p class="font-bold text-green-600 dark:text-green-400">{{ number_format($booking->deposit_amount, 0, ',', '.') }} đ</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Tổng tiền</p>
                                <p class="font-bold text-primary-600 dark:text-primary-400 text-lg">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</p>
                            </div>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="mt-6">
                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Ghi chú của khách:</p>
                        <p class="text-zinc-600 dark:text-zinc-400 italic bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl mt-2 border border-zinc-200 dark:border-zinc-800">{{ $booking->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Khối lịch sử thanh toán -->
                <div class="card-base p-8">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Lịch Sử Thanh Toán</h3>
                    @if($booking->payments->count() > 0)
                        <div class="space-y-4">
                            @foreach($booking->payments as $payment)
                                <div class="flex justify-between items-center p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                                    <div>
                                        <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($payment->amount, 0, ',', '.') }} đ <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400 ml-2 uppercase">{{ $payment->gateway }}</span></p>
                                        <!-- Đã fix Lỗi 12: Dùng toán tử ?-> để an toàn khi updated_at bị null -->
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Mã GD: {{ $payment->gateway_txn_id ?? 'N/A' }} | Cập nhật lúc: {{ $payment->updated_at?->format('H:i d/m/Y') ?? 'Chưa xác định' }}</p>
                                    </div>
                                    <div>
                                        @if($payment->status === 'success')
                                            <x-badge variant="success">Thành công</x-badge>
                                        @else
                                            <x-badge variant="warning">{{ $payment->status }}</x-badge>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-zinc-500 dark:text-zinc-400 italic text-center py-4">Chưa có giao dịch thanh toán nào được ghi nhận.</p>
                    @endif
                </div>
            </div>

            <!-- Cột phải: Form cập nhật trạng thái -->
            <div class="space-y-6">
                <div class="card-base p-8 lg:sticky lg:top-8">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Thao Tác Đơn</h3>

                    <div class="mb-6">
                        <p class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-2">Trạng thái hiện tại:</p>
                        @if($booking->isPending())
                            <x-badge variant="warning" class="px-4 py-2 text-sm">Chờ xử lý</x-badge>
                        @elseif($booking->isConfirmed())
                            <x-badge variant="info" class="px-4 py-2 text-sm">Đã xác nhận</x-badge>
                        @elseif($booking->isCompleted())
                            <x-badge variant="success" class="px-4 py-2 text-sm">Hoàn thành</x-badge>
                        @elseif($booking->isCancelled())
                            <div class="text-center">
                                <x-badge variant="danger" class="px-4 py-2 text-sm">Đã hủy</x-badge>
                                <p class="text-xs text-red-500 dark:text-red-400 mt-2 italic">Lý do: {{ $booking->cancel_reason }}</p>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('owner.bookings.update', $booking) }}" method="POST" x-data="{ status: '{{ $booking->status }}' }">
                        @csrf
                        @method('PUT')

                        <label class="label-eyebrow block mb-2">Đổi trạng thái mới</label>
                        <select name="status" x-model="status" class="input-base mb-4">
                            <option value="pending">Chờ xử lý</option>
                            <option value="confirmed">Xác nhận đơn</option>
                            <option value="completed">Đã đá / Hoàn thành</option>
                            <option value="cancelled">Hủy đơn</option>
                        </select>

                        <!-- Khung nhập lý do hủy chỉ hiện khi chọn Hủy đơn -->
                        <div x-show="status === 'cancelled'" class="mb-4" x-transition>
                            <label for="cancel-reason" class="label-eyebrow block mb-2">Lý do hủy (Bắt buộc) <span class="text-red-500">*</span></label>
                            <input id="cancel-reason" type="text" name="cancel_reason" placeholder="VD: Sân ngập nước, Khách gọi hủy..." x-bind:required="status === 'cancelled'" class="input-base">
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            Lưu Trạng Thái
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-owner-layout>
