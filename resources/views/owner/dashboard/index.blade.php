<x-owner-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
            {{ __('Tổng Quan Chủ Sân') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Xin chào, {{ auth()->user()->name }}! Dưới đây là tình hình hoạt động kinh doanh của bạn hôm nay.
        </p>
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <!-- Các thẻ Thống kê (Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Thẻ 1: Khu sân -->
            <div class="card-base p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tổng Khu Sân</p>
                    <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ $totalVenues }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-2xl flex items-center justify-center font-bold text-xl">🏟️</div>
            </div>

            <!-- Thẻ 2: Sân con -->
            <div class="card-base p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tổng Sân Con</p>
                    <p class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">{{ $totalCourts }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center font-bold text-xl">⚽</div>
            </div>

            <!-- Thẻ 3: Đơn hôm nay -->
            <div class="card-base p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Đơn Sân Hôm Nay</p>
                    <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $todayBookingsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center font-bold text-xl">📅</div>
            </div>

            <!-- Thẻ 4: Doanh thu tháng -->
            <div class="card-base p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Doanh Thu Tháng Này</p>
                    <p class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-1">{{ number_format($monthlyRevenue, 0, ',', '.') }} đ</p>
                </div>
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-2xl flex items-center justify-center font-bold text-xl">💰</div>
            </div>
        </div>

        <!-- Phím tắt nhanh chức năng -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('owner.venues.index') }}" class="card-base card-hover p-5 group">
                <h4 class="font-bold text-zinc-900 dark:text-zinc-50 text-base group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Quản lý Khu Sân &rarr;</h4>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Thêm sửa xóa thông tin cơ sở và sân.</p>
            </a>
            <a href="{{ route('owner.bookings.index') }}" class="card-base card-hover p-5 group">
                <h4 class="font-bold text-zinc-900 dark:text-zinc-50 text-base group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Đơn Đặt Sân &rarr;</h4>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Xác nhận đơn, kiểm tra thanh toán.</p>
            </a>
            <a href="{{ route('owner.schedule.index') }}" class="card-base card-hover p-5 group">
                <h4 class="font-bold text-zinc-900 dark:text-zinc-50 text-base group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Lịch Biểu &rarr;</h4>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kiểm tra khung giờ trống và lịch đá.</p>
            </a>
            <a href="{{ route('owner.reports.index') }}" class="card-base card-hover p-5 group">
                <h4 class="font-bold text-zinc-900 dark:text-zinc-50 text-base group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Báo Cáo &rarr;</h4>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Xem biểu đồ doanh thu và tình hình.</p>
            </a>
        </div>

        <!-- Bảng danh sách đơn đặt sân gần đây -->
        <div class="card-base">
            <div class="flex justify-between items-center px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Đơn Đặt Sân Gần Đây</h3>
                <a href="{{ route('owner.bookings.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">Xem tất cả &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Mã Đơn</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Khách Hàng</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Sân</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Ngày & Giờ Đá</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Trạng Thái</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($recentBookings as $booking)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-primary-600 dark:text-primary-400">#{{ $booking->code }}</td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $booking->court->name }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 text-sm">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }})
                            </td>
                            <td class="px-6 py-4 text-center">
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
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('owner.bookings.show', $booking) }}" class="btn-ghost text-xs">Chi tiết</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8">
                                <x-empty-state icon="📅" title="Chưa có giao dịch đặt sân nào" description="Các đơn đặt sân gần đây sẽ xuất hiện tại đây." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-owner-layout>
