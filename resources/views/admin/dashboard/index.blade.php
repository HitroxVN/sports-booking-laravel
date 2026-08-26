<x-admin-layout :title="'Tổng quan'">

    {{-- 6 stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Khách Hàng</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalUsers) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Chủ Sân</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalOwners) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Khu Sân</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalVenues) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Chờ Duyệt</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">
                {{ number_format($pendingVenues) }}
                @if($pendingVenues > 0)
                    <span class="inline-block align-middle ml-2 text-xs font-bold bg-red-100 text-red-600 px-2 py-1 rounded-full">cần xử lý</span>
                @endif
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Đơn</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalBookings) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Doanh Thu Tháng Này</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($monthRevenue, 0, ',', '.') }} đ</p>
        </div>
    </div>

    {{-- Bar chart doanh thu 6 tháng --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Doanh Thu 6 Tháng Gần Nhất</h3>
        @if(collect($revenueChart)->sum('amount') > 0)
            <div class="h-72">
                <canvas id="revenueChart"></canvas>
            </div>
        @else
            <div class="h-72 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-gray-400 text-sm font-medium">Chưa có doanh thu trong 6 tháng gần nhất.</p>
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
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => Math.round(v).toLocaleString('vi-VN') + ' đ'
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-admin-layout>
