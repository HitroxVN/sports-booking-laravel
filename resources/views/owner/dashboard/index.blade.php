<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Tổng Quan Chủ Sân') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Lời chào -->
            <div class="mb-8">
                <h3 class="text-2xl font-extrabold text-gray-800">Xin chào, {{ auth()->user()->name }}! 👋</h3>
                <p class="text-gray-500 text-sm mt-1">Dưới đây là tình hình hoạt động kinh doanh sân thể thao của bạn hôm nay.</p>
            </div>

            <!-- Các thẻ Thống kê (Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Thẻ 1: Khu sân -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Khu Sân</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalVenues }}</p>
                    </div>
                    <div class="w-12 h-12 bg-pink-50 text-pink-500 rounded-2xl flex items-center justify-center font-bold text-xl">🏟️</div>
                </div>

                <!-- Thẻ 2: Sân con -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Sân Con</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalCourts }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center font-bold text-xl">⚽</div>
                </div>

                <!-- Thẻ 3: Đơn hôm nay -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Đơn Sân Hôm Nay</p>
                        <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $todayBookingsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center font-bold text-xl">📅</div>
                </div>

                <!-- Thẻ 4: Doanh thu tháng -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Doanh Thu Tháng Này</p>
                        <p class="text-2xl font-extrabold text-pink-600 mt-1">{{ number_format($monthlyRevenue, 0, ',', '.') }} đ</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center font-bold text-xl">💰</div>
                </div>
            </div>

            <!-- Phím tắt nhanh chức năng -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <a href="{{ route('owner.venues.index') }}" class="p-6 bg-white rounded-3xl shadow-sm border border-pink-100 hover:border-pink-300 transition group">
                    <h4 class="font-bold text-gray-800 text-lg group-hover:text-pink-600 transition">Quản lý Khu Sân &rarr;</h4>
                    <p class="text-sm text-gray-500 mt-1">Thêm sửa xóa thông tin cơ sở và xem các sân con.</p>
                </a>
                <a href="{{ route('owner.bookings.index') }}" class="p-6 bg-white rounded-3xl shadow-sm border border-pink-100 hover:border-pink-300 transition group">
                    <h4 class="font-bold text-gray-800 text-lg group-hover:text-pink-600 transition">Quản lý Đơn Đặt Sân &rarr;</h4>
                    <p class="text-sm text-gray-500 mt-1">Xác nhận đơn, kiểm tra thanh toán và hủy lịch.</p>
                </a>
                <a href="{{ route('owner.schedule.index') }}" class="p-6 bg-white rounded-3xl shadow-sm border border-pink-100 hover:border-pink-300 transition group">
                    <h4 class="font-bold text-gray-800 text-lg group-hover:text-pink-600 transition">Xem Lịch Biểu Trực Quan &rarr;</h4>
                    <p class="text-sm text-gray-500 mt-1">Kiểm tra khung giờ trống và lịch đá trong ngày.</p>
                </a>
            </div>

            <!-- Bảng danh sách đơn đặt sân gần đây -->
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-extrabold text-gray-800">Đơn Đặt Sân Gần Đây</h3>
                    <a href="{{ route('owner.bookings.index') }}" class="text-sm font-bold text-pink-500 hover:text-pink-700">Xem tất cả &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                                <th class="p-4 font-semibold text-sm">Mã Đơn</th>
                                <th class="p-4 font-semibold text-sm">Khách Hàng</th>
                                <th class="p-4 font-semibold text-sm">Sân</th>
                                <th class="p-4 font-semibold text-sm">Ngày & Giờ Đá</th>
                                <th class="p-4 font-semibold text-sm text-center">Trạng Thái</th>
                                <th class="p-4 font-semibold text-sm text-right">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50">
                            @forelse($recentBookings as $booking)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="p-4 font-bold text-pink-600">#{{ $booking->code }}</td>
                                <td class="p-4 font-medium text-gray-800">{{ $booking->user->name ?? 'Khách lẻ' }}</td>
                                <td class="p-4 text-gray-600">{{ $booking->court->name }}</td>
                                <td class="p-4 text-gray-600 text-sm">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} 
                                    ({{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }})
                                </td>
                                <td class="p-4 text-center">
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
                                <td class="p-4 text-right">
                                    <a href="{{ route('owner.bookings.show', $booking) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-bold transition">Chi tiết</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-gray-400">Chưa có giao dịch đặt sân nào gần đây.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>