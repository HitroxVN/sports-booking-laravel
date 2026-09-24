<x-owner-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ __('Báo cáo & Thống kê Doanh thu') }}
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Theo dõi biến động dòng tiền và lịch sử giao dịch thanh toán.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- BỘ LỌC TÌM KIẾM -->
        <div class="card-base p-5">
            <form action="{{ route('owner.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="label-eyebrow block mb-1.5">Khu Sân</label>
                    <select name="venue_id" class="input-base">
                        <option value="">-- Tất cả khu sân --</option>
                        @foreach($venues as $v)
                            <option value="{{ $v->id }}" @selected($venueId == $v->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label-eyebrow block mb-1.5">Từ Ngày</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="input-base">
                </div>

                <div>
                    <label class="label-eyebrow block mb-1.5">Đến Ngày</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="input-base">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1">Lọc Dữ Liệu</button>
                    <a href="{{ route('owner.reports.index') }}" class="btn-secondary">Đặt lại</a>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                <a href="{{ route('owner.reports.export', request()->only(['start_date', 'end_date', 'venue_id'])) }}"
                   class="btn-secondary text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Xuất file CSV
                </a>
            </div>
        </div>

        <!-- CARDS THỐNG KÊ (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tổng Doanh Thu</p>
                <p class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-2">
                    {{ number_format($totalRevenue, 0, ',', '.') }} <span class="text-lg font-bold">đ</span>
                </p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Từ các giao dịch thành công</p>
            </div>

            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Giao Dịch Thành Công</p>
                <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">
                    {{ number_format($successfulTransactions) }}
                </p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Lượt thanh toán hoàn tất</p>
            </div>

            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Đơn Đặt Sân Đã Thu</p>
                <p class="text-3xl font-extrabold text-primary-600 dark:text-primary-400 mt-2">
                    {{ number_format($totalBookings) }}
                </p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Đơn phát sinh doanh thu</p>
            </div>
        </div>

        <!-- KHU VỰC BIỂU ĐỒ & PHƯƠNG THỨC THANH TOÁN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card-base p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-zinc-900 dark:text-zinc-50">Biến Động Doanh Thu</h3>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $startDate }} &rarr; {{ $endDate }}</span>
                </div>
                <div class="h-72">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="card-base p-6">
                <h3 class="font-bold text-zinc-900 dark:text-zinc-50 mb-4">Phương Thức Thanh Toán</h3>
                <div class="space-y-4">
                    @forelse($revenueByGateway as $gw)
                        @php
                            $percent = $totalRevenue > 0 ? ($gw->total / $totalRevenue) * 100 : 0;
                        @endphp
                        <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300">{{ $gw->gateway }}</span>
                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($gw->total, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-2 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                                <span>{{ $gw->count }} giao dịch</span>
                                <span>{{ round($percent, 1) }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 text-center py-8">Chưa có dữ liệu thanh toán.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- BẢNG LỊCH SỬ GIAO DỊCH -->
        <div class="card-base overflow-hidden">
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="font-bold text-zinc-900 dark:text-zinc-50">Lịch Sử Giao Dịch Gần Đây</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                            <th class="py-3 px-4">Mã Giao Dịch</th>
                            <th class="py-3 px-4">Khách Hàng</th>
                            <th class="py-3 px-4">Khu Vực & Sân</th>
                            <th class="py-3 px-4">Phương Thức</th>
                            <th class="py-3 px-4">Số Tiền</th>
                            <th class="py-3 px-4">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        @forelse($recentPayments as $p)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $p->gateway_txn_id ?? ('#' . $p->id) }}
                                </td>
                                <td class="py-3.5 px-4 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $p->booking->user->name ?? 'Khách lẻ' }}
                                </td>
                                <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-400">
                                    {{ $p->booking->court->venue->name ?? 'N/A' }}
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">({{ $p->booking->court->name ?? 'Sân' }})</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                        {{ $p->gateway }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-green-600 dark:text-green-400">
                                    +{{ number_format($p->amount, 0, ',', '.') }} đ
                                </td>
                                <td class="py-3.5 px-4 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $p->created_at->format('H:i d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-zinc-400 dark:text-zinc-500">Không có giao dịch nào trong khoảng thời gian này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentPayments->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $recentPayments->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Chart.js CDN & Render Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(63, 63, 70, 0.4)' : 'rgba(0, 0, 0, 0.06)';
            const tickColor = isDark ? '#a1a1aa' : '#71717a';
            const lineColor = isDark ? '#4ade80' : '#16a34a';

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, isDark ? 'rgba(74, 222, 128, 0.2)' : 'rgba(22, 163, 74, 0.15)');
            gradient.addColorStop(1, 'rgba(0, 0, 0, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: {!! json_encode($chartValues) !!},
                        borderColor: lineColor,
                        borderWidth: 2,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: lineColor
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
                                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tickColor } },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: {
                                color: tickColor,
                                callback: function(value) {
                                    return value >= 1000000 ? (value / 1000000) + 'M' : (value >= 1000 ? (value / 1000) + 'k' : value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-owner-layout>