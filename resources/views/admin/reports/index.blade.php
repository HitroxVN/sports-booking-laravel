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

    {{-- Biểu đồ Doanh thu & Tỷ trọng Thanh toán --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 mb-8">
        {{-- Biểu đồ doanh thu theo ngày (8/12) --}}
        <div class="lg:col-span-8 card-base p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-zinc-100 dark:border-zinc-800 gap-2">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        <span>Biến động doanh thu theo ngày</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Doanh thu thanh toán thành công từng ngày trong kỳ</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[11px] text-zinc-400 uppercase font-medium block">Kỳ báo cáo:</span>
                    <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $fromDate->format('d/m/Y') }} – {{ $toDate->format('d/m/Y') }}</span>
                </div>
            </div>

            @if(count($chartValues) > 0)
                <div class="h-80 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            @else
                <div class="h-80 flex flex-col items-center justify-center bg-zinc-50/50 dark:bg-zinc-800/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Chưa ghi nhận doanh thu trong khoảng thời gian này.</p>
                </div>
            @endif
        </div>

        {{-- Tỷ trọng phương thức thanh toán (4/12) --}}
        <div class="lg:col-span-4 card-base p-6 flex flex-col justify-between">
            <div class="pb-4 mb-4 border-b border-zinc-100 dark:border-zinc-800">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    <span>Tỷ trọng phương thức thanh toán</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Phân bổ nguồn thu theo cổng và kênh thanh toán</p>
            </div>

            @if(($paymentBreakdown['total'] ?? 0) > 0)
                <div class="h-48 relative flex items-center justify-center mb-4">
                    <canvas id="paymentBreakdownChart"></canvas>
                </div>

                {{-- Chú giải số liệu chi tiết --}}
                <div class="space-y-2.5 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    @foreach($paymentBreakdown['items'] as $item)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $item['label'] }}</span>
                            </div>
                            <div class="text-right flex items-center gap-2">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($item['amount'], 0, ',', '.') }} đ
                                </span>
                                <span class="text-[11px] font-semibold text-zinc-400 w-10 text-right">
                                    {{ $item['percentage'] }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center bg-zinc-50/50 dark:bg-zinc-800/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Chưa có dữ liệu thanh toán trong kỳ.</p>
                </div>
            @endif
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartLabels = @json($chartLabels);
                const chartValues = @json($chartValues);
                const paymentBreakdown = @json($paymentBreakdown);

                // 1. Biểu đồ đường doanh thu theo ngày
                const revCanvas = document.getElementById('revenueChart');
                if (revCanvas && chartValues.length) {
                    const ctx = revCanvas.getContext('2d');
                    const isDark = document.documentElement.classList.contains('dark');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, isDark ? 'rgba(16, 185, 129, 0.35)' : 'rgba(16, 185, 129, 0.25)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: chartValues,
                                borderColor: isDark ? '#34d399' : '#059669',
                                borderWidth: 2,
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: isDark ? '#34d399' : '#059669'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Doanh thu: ' + Math.round(context.parsed.y).toLocaleString('vi-VN') + ' đ';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: '#a1a1aa', maxRotation: 45, font: { size: 11 } } },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(113, 113, 122, 0.12)' },
                                    ticks: {
                                        color: '#a1a1aa',
                                        font: { size: 11 },
                                        callback: v => (v >= 1000000 ? (v / 1000000).toFixed(1) + 'M' : (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)) + ' đ'
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Biểu đồ tròn tỷ trọng thanh toán
                const payCanvas = document.getElementById('paymentBreakdownChart');
                if (payCanvas && paymentBreakdown && paymentBreakdown.total > 0) {
                    new Chart(payCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: paymentBreakdown.items.map(i => i.label),
                            datasets: [{
                                data: paymentBreakdown.items.map(i => i.amount),
                                backgroundColor: paymentBreakdown.items.map(i => i.color),
                                borderWidth: 2,
                                borderColor: document.documentElement.classList.contains('dark') ? '#18181b' : '#ffffff',
                                hoverOffset: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const val = Math.round(context.raw).toLocaleString('vi-VN') + ' đ';
                                            const pct = paymentBreakdown.items[context.dataIndex]?.percentage || 0;
                                            return ` ${context.label}: ${val} (${pct}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>
