<x-admin-layout :title="'Tổng quan Dashboard'">

    {{-- Page header + Period Filter Tabs --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Tổng quan hệ thống</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Thống kê doanh thu đa chiều và hiệu quả hoạt động của nền tảng</p>
        </div>

        {{-- Period Filter Buttons --}}
        <div class="inline-flex p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 shadow-sm self-start sm:self-auto">
            <a href="{{ route('admin.dashboard', ['period' => 'day']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $period === 'day' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                Theo Ngày
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $period === 'month' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                Theo Tháng
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'year']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $period === 'year' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                Theo Năm
            </a>
        </div>
    </div>

    {{-- 6 stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Khách Hàng</p>
            <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ number_format($totalUsers) }}</p>
        </div>

        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Chủ Sân</p>
            <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ number_format($totalOwners) }}</p>
        </div>

        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Khu Sân</p>
            <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ number_format($totalVenues) }}</p>
        </div>

        @php $pendingVenueUrl = route('admin.venues.index', ['status' => 'pending']); @endphp
        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Chờ Duyệt</p>
            <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">
                @if($pendingVenues > 0)
                    <a href="{{ $pendingVenueUrl }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        {{ number_format($pendingVenues) }}
                        <x-badge variant="danger" class="align-middle ml-1 text-[10px]">xử lý &rarr;</x-badge>
                    </a>
                @else
                    {{ number_format($pendingVenues) }}
                @endif
            </p>
        </div>

        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tổng Đơn</p>
            <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ number_format($totalBookings) }}</p>
        </div>

        <div class="card-base p-4">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Doanh Thu Tháng</p>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($monthRevenue, 0, ',', '.') }} đ</p>
        </div>
    </div>

    {{-- 2 Biểu đồ Thống kê Doanh thu & Tỷ trọng Thanh toán --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        {{-- Biểu đồ Doanh thu (8/12) --}}
        <div class="lg:col-span-8 card-base p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-zinc-100 dark:border-zinc-800 gap-2">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        <span>{{ $revenueChart['title'] }}</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Biểu đồ thể hiện biến động dòng tiền theo các mốc thời gian</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[11px] text-zinc-400 uppercase font-medium block">Tổng doanh thu kỳ:</span>
                    <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ number_format($revenueChart['totalPeriod'] ?? 0, 0, ',', '.') }} đ
                    </span>
                </div>
            </div>

            @if(($revenueChart['totalPeriod'] ?? 0) > 0)
                <div class="h-80 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            @else
                <div class="h-80 flex flex-col items-center justify-center bg-zinc-50/50 dark:bg-zinc-800/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                    <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Chưa ghi nhận doanh thu trong khoảng thời gian này.</p>
                </div>
            @endif
        </div>

        {{-- Biểu đồ Tỷ trọng Phương thức thanh toán (4/12) --}}
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const revenueChartData = @json($revenueChart);
                const paymentBreakdownData = @json($paymentBreakdown);

                // 1. Render Bar/Line Revenue Chart
                const revCanvas = document.getElementById('revenueChart');
                if (revCanvas && revenueChartData && revenueChartData.amounts && revenueChartData.amounts.length) {
                    const ctx = revCanvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.45)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

                    new Chart(ctx, {
                        type: revenueChartData.type === 'day' ? 'line' : 'bar',
                        data: {
                            labels: revenueChartData.labels,
                            datasets: [{
                                label: 'Doanh thu',
                                data: revenueChartData.amounts,
                                backgroundColor: revenueChartData.type === 'day' ? gradient : 'rgba(16, 185, 129, 0.75)',
                                borderColor: '#10b981',
                                borderWidth: 2,
                                fill: revenueChartData.type === 'day',
                                tension: 0.35,
                                borderRadius: 6,
                                pointBackgroundColor: '#10b981',
                                pointRadius: revenueChartData.type === 'day' ? 3 : 0,
                                pointHoverRadius: 6,
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
                                            return 'Doanh thu: ' + Math.round(context.raw).toLocaleString('vi-VN') + ' đ';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: '#a1a1aa',
                                        maxRotation: 45,
                                        font: { size: 11 }
                                    },
                                    grid: { display: false }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#a1a1aa',
                                        font: { size: 11 },
                                        callback: v => (v >= 1000000 ? (v / 1000000).toFixed(1) + 'M' : (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)) + ' đ'
                                    },
                                    grid: { color: 'rgba(113, 113, 122, 0.12)' }
                                }
                            }
                        }
                    });
                }

                // 2. Render Doughnut Payment Breakdown Chart
                const payCanvas = document.getElementById('paymentBreakdownChart');
                if (payCanvas && paymentBreakdownData && paymentBreakdownData.amounts && paymentBreakdownData.total > 0) {
                    new Chart(payCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: paymentBreakdownData.labels,
                            datasets: [{
                                data: paymentBreakdownData.amounts,
                                backgroundColor: paymentBreakdownData.colors,
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
                                            const pct = paymentBreakdownData.items[context.dataIndex]?.percentage || 0;
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
