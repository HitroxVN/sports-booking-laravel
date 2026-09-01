<x-admin-layout :title="'Báo cáo'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Báo cáo</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Thống kê doanh thu theo kỳ</p>
    </div>

    {{-- Filter bar --}}
    <div class="card-base p-4 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="report-from" class="label-eyebrow block mb-1">Từ ngày</label>
                <input id="report-from" type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}"
                       class="input-base w-auto">
            </div>
            <div>
                <label for="report-to" class="label-eyebrow block mb-1">Đến ngày</label>
                <input id="report-to" type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}"
                       class="input-base w-auto">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Xem báo cáo</button>
                <a href="{{ route('admin.reports.export', request()->only(['from_date', 'to_date'])) }}"
                   class="px-4 py-2.5 inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200">Export CSV</a>
            </div>
        </form>
    </div>

    {{-- 3 stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Doanh Thu</p>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ number_format($totalRevenue, 0, ',', '.') }} đ</p>
        </div>
        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Số Đơn</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ number_format($totalBookings) }}</p>
        </div>
        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Tiền Cọc</p>
            <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-2">{{ number_format($totalDeposit, 0, ',', '.') }} đ</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8">
        {{-- Top 5 khu sân --}}
        <div class="card-base">
            <div class="p-6 pb-3">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Top 5 Khu Sân Doanh Thu</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $fromDate->format('d/m/Y') }} – {{ $toDate->format('d/m/Y') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold w-16">Hạng</th>
                            <th class="p-4 font-semibold">Tên khu sân</th>
                            <th class="p-4 font-semibold text-right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($topVenues as $i => $v)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="p-4">
                                <span class="w-7 h-7 inline-flex items-center justify-center rounded-full text-xs font-bold
                                             {{ $i === 0 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : ($i === 1 ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300' : ($i === 2 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400')) }}">{{ $i + 1 }}</span>
                            </td>
                            <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-50">{{ $v['name'] }}</td>
                            <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-50 text-right">{{ number_format($v['revenue'], 0, ',', '.') }} đ</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-4">
                                <x-empty-state icon="heroicons-o-inbox" title="Chưa có dữ liệu trong kỳ"
                                               description="Điều chỉnh khoảng thời gian để xem báo cáo." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Chi tiết đơn trong kỳ --}}
        <div class="card-base">
            <div class="p-6 pb-3">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Chi Tiết Đơn Trong Kỳ</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Mã đơn</th>
                            <th class="p-4 font-semibold">Khách hàng</th>
                            <th class="p-4 font-semibold">Khu sân</th>
                            <th class="p-4 font-semibold">Ngày đặt</th>
                            <th class="p-4 font-semibold text-right">Tổng tiền</th>
                            <th class="p-4 font-semibold">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="p-4 font-bold text-primary-600 dark:text-primary-400">#{{ $booking->code }}</td>
                            <td class="p-4 text-zinc-900 dark:text-zinc-50">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                            <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ $booking->court->venue->name ?? '—' }}</td>
                            <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-50 text-right">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                            <td class="p-4">
                                @if($booking->isConfirmed())
                                    <x-badge variant="info">Đã xác nhận</x-badge>
                                @else
                                    <x-badge variant="success">Hoàn thành</x-badge>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4">
                                <x-empty-state icon="heroicons-o-calendar" title="Chưa có dữ liệu trong kỳ"
                                               description="Điều chỉnh khoảng thời gian để xem báo cáo." />
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
    </div>
</x-admin-layout>
