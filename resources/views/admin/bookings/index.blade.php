<x-admin-layout :title="'Đơn đặt sân'">

    {{-- Filter bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tìm mã đơn</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã đơn..."
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Trạng thái</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Đã xác nhận</option>
                    <option value="completed" @selected(request('status') === 'completed')>Hoàn thành</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">Lọc</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng read-only --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Mã đơn</th>
                        <th class="p-4 font-semibold">Khách hàng</th>
                        <th class="p-4 font-semibold">Khu sân › Sân con</th>
                        <th class="p-4 font-semibold">Ngày đặt</th>
                        <th class="p-4 font-semibold">Giờ</th>
                        <th class="p-4 font-semibold text-right">Tổng tiền</th>
                        <th class="p-4 font-semibold">Trạng thái</th>
                        <th class="p-4 font-semibold">Thanh toán</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-blue-600">#{{ $booking->code }}</td>
                        <td class="p-4 font-medium text-gray-800">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                        <td class="p-4 text-gray-600 text-sm">{{ $booking->court->venue->name ?? '—' }} › {{ $booking->court->name ?? '—' }}</td>
                        <td class="p-4 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                        <td class="p-4 text-gray-800 font-semibold text-right">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                        <td class="p-4">
                            @if($booking->isPending())
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Chờ xử lý</span>
                            @elseif($booking->isConfirmed())
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Đã xác nhận</span>
                            @elseif($booking->isCompleted())
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Hoàn thành</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Đã hủy</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($booking->isPaid())
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Đã thanh toán</span>
                            @elseif($booking->hasDeposit())
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Đã cọc</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">Chưa thanh toán</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-gray-400">Không có đơn đặt sân nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
