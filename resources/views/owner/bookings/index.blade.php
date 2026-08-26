<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Quản lý Khu Sân') }}
        </h2>
        <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-sm hover:bg-pink-50 hover:text-pink-600 transition">
            &larr; Trở về trang chủ
        </a>
    </div>
</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Danh Sách Đơn Hàng</h3>
                    <p class="text-gray-500 text-sm mt-1">Theo dõi và quản lý các lượt khách đặt sân</p>
                </div>
                
                <!-- Bộ lọc trạng thái -->
                <div class="flex space-x-2">
                    <a href="{{ route('owner.bookings.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ !request('status') ? 'bg-pink-500 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-pink-50' }}">Tất cả</a>
                    <a href="{{ route('owner.bookings.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request('status') == 'pending' ? 'bg-yellow-500 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-yellow-50' }}">Chờ xử lý</a>
                    <a href="{{ route('owner.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ request('status') == 'confirmed' ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-blue-50' }}">Đã xác nhận</a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                                <th class="p-5 font-semibold text-sm">Mã Đơn / Ngày Đặt</th>
                                <th class="p-5 font-semibold text-sm">Khách Hàng</th>
                                <th class="p-5 font-semibold text-sm">Thông Tin Sân</th>
                                <th class="p-5 font-semibold text-sm">Tổng Tiền</th>
                                <th class="p-5 font-semibold text-sm text-center">Trạng Thái</th>
                                <th class="p-5 font-semibold text-sm text-right">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50">
                            @forelse($bookings as $booking)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="font-bold text-pink-600 text-base">#{{ $booking->code }}</div>
                                    <!-- Đã fix Lỗi 10: Dùng toán tử ?-> để an toàn khi created_at bị null -->
                                    <div class="text-xs text-gray-500 mt-1">{{ $booking->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-medium text-gray-800">{{ $booking->user->name ?? 'Khách lẻ' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $booking->user->phone ?? '' }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-medium text-gray-700">{{ $booking->court->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} | 
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="p-5 font-bold text-gray-800">
                                    {{ number_format($booking->total_amount, 0, ',', '.') }} đ
                                </td>
                                <td class="p-5 text-center">
                                    @if($booking->isPending())
                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Chờ xử lý</span>
                                    @elseif($booking->isConfirmed())
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Đã xác nhận</span>
                                    @elseif($booking->isCompleted())
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Hoàn thành</span>
                                    @elseif($booking->isCancelled())
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Đã hủy</span>
                                    @endif
                                </td>
                                <td class="p-5 text-right font-medium">
                                    <a href="{{ route('owner.bookings.show', $booking) }}" class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800 rounded-lg transition-colors border border-indigo-100">Chi tiết</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-500">
                                    <div class="mb-4 text-pink-200 flex justify-center">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700">Chưa có đơn đặt sân nào</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>