<x-admin-layout :title="'Tổng quan'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Tổng quan</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Thống kê tổng thể của hệ thống đặt sân</p>
    </div>

    {{-- 6 stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Khách Hàng</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ number_format($totalUsers) }}</p>
        </div>

        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Chủ Sân</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ number_format($totalOwners) }}</p>
        </div>

        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Khu Sân</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ number_format($totalVenues) }}</p>
        </div>

        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Chờ Duyệt</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">
                {{ number_format($pendingVenues) }}
                @if($pendingVenues > 0)
                    <x-badge variant="danger" class="align-middle ml-2">cần xử lý</x-badge>
                @endif
            </p>
        </div>

        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tổng Đơn</p>
            <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ number_format($totalBookings) }}</p>
        </div>

        <div class="card-base p-5">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Doanh Thu Tháng Này</p>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ number_format($monthRevenue, 0, ',', '.') }} đ</p>
        </div>
    </div>

    {{-- Bar chart doanh thu 6 tháng --}}
    <div class="card-base p-6">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Doanh Thu 6 Tháng Gần Nhất</h3>
        @if(collect($revenueChart)->sum('amount') > 0)
            <div class="h-72">
                <canvas id="revenueChart"></canvas>
            </div>
        @else
            <div class="h-72 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700">
                <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium">Chưa có doanh thu trong 6 tháng gần nhất.</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const revenueData = @json($revenueChart);
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: revenueData.map(d => d.label),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: revenueData.map(d => d.amount),
                        backgroundColor: 'rgba(22, 163, 74, 0.6)',
                        borderColor: 'rgb(22, 163, 74)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: { color: '#a1a1aa' },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#a1a1aa',
                                callback: v => Math.round(v).toLocaleString('vi-VN') + ' đ'
                            },
                            grid: { color: 'rgba(113, 113, 122, 0.15)' }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-admin-layout>
