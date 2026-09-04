<x-owner-layout>
    <div class="space-y-6">

        <!-- HEADER ĐỒNG BỘ VỚI LỊCH BIỂU -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Báo cáo & Thống kê Doanh thu</h1>
                <p class="text-sm text-gray-500 mt-1">Theo dõi biến động dòng tiền và lịch sử giao dịch thanh toán.</p>
            </div>
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-3.5 py-2 rounded-xl shadow-xs transition hover:bg-gray-50">
                    &larr; Trở về trang chủ
                </a>
            </div>
        </div>

        <!-- BỘ LỌC TÌM KIẾM -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('owner.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Khu Sân</label>
                    <select name="venue_id" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        <option value="">-- Tất cả khu sân --</option>
                        @foreach($venues as $v)
                            <option value="{{ $v->id }}" @selected($venueId == $v->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Từ Ngày</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Đến Ngày</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                        Lọc Dữ Liệu
                    </button>
                    <a href="{{ route('owner.reports.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 font-medium text-sm px-3.5 py-2.5 rounded-xl transition flex items-center justify-center">
                        Đặt lại
                    </a>
                </div>
            </form>
        </div>

        <!-- CARDS THỐNG KÊ (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tổng Doanh Thu</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-2">
                    {{ number_format($totalRevenue, 0, ',', '.') }} <span class="text-lg font-bold">đ</span>
                </p>
                <p class="text-xs text-gray-400 mt-1">Từ các giao dịch thành công</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Giao Dịch Thành Công</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-2">
                    {{ number_format($successfulTransactions) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Lượt thanh toán hoàn tất</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Đơn Đặt Sân Đã Thu</p>
                <p class="text-3xl font-extrabold text-indigo-600 mt-2">
                    {{ number_format($totalBookings) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Đơn phát sinh doanh thu</p>
            </div>
        </div>

        <!-- KHU VỰC BIỂU ĐỒ & PHƯƠNG THỨC THANH TOÁN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Biến Động Doanh Thu</h3>
                    <span class="text-xs text-gray-500">{{ $startDate }} &rarr; {{ $endDate }}</span>
                </div>
                <div class="h-72">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4">Phương Thức Thanh Toán</h3>
                <div class="space-y-4">
                    @forelse($revenueByGateway as $gw)
                        @php
                            $percent = $totalRevenue > 0 ? ($gw->total / $totalRevenue) * 100 : 0;
                        @endphp
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-xs uppercase text-gray-700">{{ $gw->gateway }}</span>
                                <span class="text-xs font-bold text-gray-900">{{ number_format($gw->total, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1 text-[11px] text-gray-500">
                                <span>{{ $gw->count }} giao dịch</span>
                                <span>{{ round($percent, 1) }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Chưa có dữ liệu thanh toán.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- BẢNG LỊCH SỬ GIAO DỊCH -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Lịch Sử Giao Dịch Gần Đây</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3 px-4">Mã Giao Dịch</th>
                            <th class="py-3 px-4">Khách Hàng</th>
                            <th class="py-3 px-4">Khu Vực & Sân</th>
                            <th class="py-3 px-4">Phương Thức</th>
                            <th class="py-3 px-4">Số Tiền</th>
                            <th class="py-3 px-4">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($recentPayments as $p)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-xs text-gray-600">
                                    {{ $p->gateway_txn_id ?? ('#' . $p->id) }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-800 font-medium">
                                    {{ $p->booking->user->name ?? 'Khách lẻ' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-600">
                                    {{ $p->booking->court->venue->name ?? 'N/A' }} 
                                    <span class="text-xs text-gray-400">({{ $p->booking->court->name ?? 'Sân' }})</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase bg-gray-100 text-gray-700">
                                        {{ $p->gateway }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-emerald-600">
                                    +{{ number_format($p->amount, 0, ',', '.') }} đ
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    {{ $p->created_at->format('H:i d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-400">Không có giao dịch nào trong khoảng thời gian này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentPayments->hasPages())
                <div class="p-4 border-t border-gray-100">
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
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: {!! json_encode($chartValues) !!},
                        borderColor: '#10b981',
                        borderWidth: 2,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981'
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
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: {
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