<x-admin-layout :title="'Chi tiết đơn #' . $booking->code">

    {{-- Page header --}}
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
            &larr; Quay lại danh sách
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
            Chi tiết đơn đặt sân: <span class="text-primary-600 dark:text-primary-400">#{{ $booking->code }}</span>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Cột trái: thông tin + thanh toán --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card-base p-8">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Thông tin đơn đặt</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="label-eyebrow mb-3">Khách hàng</h4>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">{{ $booking->user->name ?? 'Khách lẻ' }}</p>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-1">{{ $booking->user->phone ?? 'Không có SĐT' }}</p>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ $booking->user->email ?? 'Không có Email' }}</p>
                        @if($booking->user)
                            <a href="{{ route('admin.users.show', $booking->user) }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline mt-2 inline-block">Xem hồ sơ khách &rarr;</a>
                        @endif
                    </div>
                    <div>
                        <h4 class="label-eyebrow mb-3">Sân thể thao</h4>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">{{ $booking->court->venue->name ?? '—' }}</p>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-1">Sân: <span class="font-semibold">{{ $booking->court->name ?? '—' }}</span></p>
                        <p class="text-zinc-600 dark:text-zinc-400">Chủ sân: {{ $booking->court->venue->owner->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-8 bg-zinc-50 dark:bg-zinc-800/50 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                    <h4 class="label-eyebrow mb-4">Thời gian &amp; giá trị</h4>
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

            {{-- Lịch sử thanh toán --}}
            <div class="card-base p-8">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Lịch sử thanh toán</h3>
                @if($booking->payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($booking->payments as $payment)
                            <div class="flex justify-between items-center p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                                <div>
                                    <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($payment->amount, 0, ',', '.') }} đ <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400 ml-2 uppercase">{{ $payment->gateway }}</span></p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Mã GD: {{ $payment->gateway_txn_id ?? 'N/A' }} | Cập nhật lúc: {{ $payment->updated_at?->format('H:i d/m/Y') ?? 'Chưa xác định' }}</p>
                                </div>
                                <div>
                                    @if($payment->status === 'success')
                                        <x-badge variant="success">Thành công</x-badge>
                                    @elseif($payment->status === 'refunded')
                                        <x-badge variant="danger">Đã hoàn tiền</x-badge>
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

        {{-- Cột phải: trạng thái (read-only) --}}
        <div class="space-y-6">
            <div class="card-base p-8 lg:sticky lg:top-8">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Trạng thái đơn</h3>

                <div class="mb-6">
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
                            @if($booking->cancelled_at)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Hủy lúc: {{ $booking->cancelled_at->format('H:i d/m/Y') }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Ngày tạo đơn</dt>
                        <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->created_at?->format('H:i d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500 dark:text-zinc-400">Thanh toán</dt>
                        <dd>
                            @if($booking->isPaid())
                                <x-badge variant="success">Đã thanh toán</x-badge>
                            @elseif($booking->hasDeposit())
                                <x-badge variant="warning">Đã cọc</x-badge>
                            @else
                                <x-badge variant="default">Chưa thanh toán</x-badge>
                            @endif
                        </dd>
                    </div>
                </dl>

                <p class="mt-6 text-xs text-zinc-400 dark:text-zinc-500 italic">Trạng thái đơn do chủ sân vận hành. Admin chỉ xem để hỗ trợ khiếu nại.</p>
            </div>
        </div>

    </div>
</x-admin-layout>
