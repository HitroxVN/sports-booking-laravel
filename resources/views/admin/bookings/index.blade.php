<x-admin-layout :title="'Đơn đặt sân'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Đơn đặt sân</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Theo dõi tất cả đơn đặt sân trên hệ thống</p>
    </div>

    {{-- Filter bar --}}
    <div class="card-base p-4 mb-6">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="label-eyebrow block mb-1">Tìm mã đơn</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã đơn..."
                       class="input-base">
            </div>
            <div>
                <label class="label-eyebrow block mb-1">Trạng thái</label>
                <select name="status" class="input-base w-auto">
                    <option value="">Tất cả</option>
                    <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Đã xác nhận</option>
                    <option value="completed" @selected(request('status') === 'completed')>Hoàn thành</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Lọc</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng read-only --}}
    <div class="card-base">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Mã đơn</th>
                        <th class="p-4 font-semibold">Khách hàng</th>
                        <th class="p-4 font-semibold">Khu sân › Sân con</th>
                        <th class="p-4 font-semibold">Ngày đặt</th>
                        <th class="p-4 font-semibold">Giờ</th>
                        <th class="p-4 font-semibold">Ngày tạo đơn</th>
                        <th class="p-4 font-semibold text-right">Tổng tiền</th>
                        <th class="p-4 font-semibold">Trạng thái</th>
                        <th class="p-4 font-semibold">Thanh toán</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="p-4 font-bold text-primary-600 dark:text-primary-400">#{{ $booking->code }}</td>
                        <td class="p-4 font-medium text-zinc-900 dark:text-zinc-50">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ $booking->court->venue->name ?? '—' }} › {{ $booking->court->name ?? '—' }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                        <td class="p-4 text-zinc-500 dark:text-zinc-400 text-sm">{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-4 text-zinc-900 dark:text-zinc-50 font-semibold text-right">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                        <td class="p-4">
                            @if($booking->isPending())
                                <x-badge variant="warning">Chờ xử lý</x-badge>
                            @elseif($booking->isConfirmed())
                                <x-badge variant="info">Đã xác nhận</x-badge>
                            @elseif($booking->isCompleted())
                                <x-badge variant="success">Hoàn thành</x-badge>
                            @else
                                <x-badge variant="danger">Đã hủy</x-badge>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($booking->isPaid())
                                <x-badge variant="success">Đã thanh toán</x-badge>
                            @elseif($booking->hasDeposit())
                                <x-badge variant="warning">Đã cọc</x-badge>
                            @else
                                <x-badge variant="default">Chưa thanh toán</x-badge>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-4">
                            <x-empty-state icon="heroicons-o-calendar" title="Không có đơn đặt sân nào"
                                           description="Không có đơn nào khớp với bộ lọc hiện tại." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
