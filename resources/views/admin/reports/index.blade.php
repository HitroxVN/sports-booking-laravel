<x-admin-layout :title="'Báo cáo'">

    {{-- Filter bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Từ ngày</label>
                <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}"
                       class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Đến ngày</label>
                <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}"
                       class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">Xem báo cáo</button>
                <a href="{{ route('admin.reports.export', request()->only(['from_date', 'to_date'])) }}"
                   class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700">Export CSV</a>
            </div>
        </form>
    </div>

    {{-- 3 stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Doanh Thu</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }} đ</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Số Đơn</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalBookings) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Tiền Cọc</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($totalDeposit, 0, ',', '.') }} đ</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top 5 khu sân --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 pb-3">
                <h3 class="text-lg font-bold text-gray-800">Top 5 Khu Sân Doanh Thu</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $fromDate->format('d/m/Y') }} – {{ $toDate->format('d/m/Y') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold w-16">Hạng</th>
                            <th class="p-4 font-semibold">Tên khu sân</th>
                            <th class="p-4 font-semibold text-right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topVenues as $i => $v)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <span class="w-7 h-7 inline-flex items-center justify-center rounded-full text-xs font-bold
                                             {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">{{ $i + 1 }}</span>
                            </td>
                            <td class="p-4 font-semibold text-gray-800">{{ $v['name'] }}</td>
                            <td class="p-4 font-semibold text-gray-800 text-right">{{ number_format($v['revenue'], 0, ',', '.') }} đ</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-12 text-center text-gray-400">Chưa có dữ liệu trong kỳ.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Chi tiết đơn trong kỳ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 pb-3">
                <h3 class="text-lg font-bold text-gray-800">Chi Tiết Đơn Trong Kỳ</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Mã đơn</th>
                            <th class="p-4 font-semibold">Khách hàng</th>
                            <th class="p-4 font-semibold">Khu sân</th>
                            <th class="p-4 font-semibold">Ngày đặt</th>
                            <th class="p-4 font-semibold text-right">Tổng tiền</th>
                            <th class="p-4 font-semibold">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-bold text-blue-600">#{{ $booking->code }}</td>
                            <td class="p-4 text-gray-800">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                            <td class="p-4 text-gray-600 text-sm">{{ $booking->court->venue->name ?? '—' }}</td>
                            <td class="p-4 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td class="p-4 font-semibold text-gray-800 text-right">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                            <td class="p-4">
                                @if($booking->isConfirmed())
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Đã xác nhận</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Hoàn thành</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-400">Chưa có dữ liệu trong kỳ.</td>
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
    </div>
</x-admin-layout>
