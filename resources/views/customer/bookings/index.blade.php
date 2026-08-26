<x-app-layout>
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Lịch sử đặt sân của tôi</h2>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Mã đơn</th>
                            <th class="px-6 py-3">Sân</th>
                            <th class="px-6 py-3">Ngày đặt</th>
                            <th class="px-6 py-3">Khung giờ</th>
                            <th class="px-6 py-3">Tổng tiền</th>
                            <th class="px-6 py-3">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-indigo-600">{{ $booking->code }}</td>
                                <td class="px-6 py-4">
                                    {{ $booking->court->name ?? 'N/A' }}
                                    <div class="text-xs text-gray-400">{{ $booking->court->venue->name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ number_format($booking->total_amount) }} VNĐ
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Bạn chưa có đơn đặt sân nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>