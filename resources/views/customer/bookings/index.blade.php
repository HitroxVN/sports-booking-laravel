<x-app-layout>
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto card-base p-6">
            <h2 class="text-2xl font-bold mb-6 text-zinc-900 dark:text-zinc-100">Lịch sử đặt sân của tôi</h2>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                    <thead class="text-xs text-zinc-700 dark:text-zinc-300 uppercase bg-zinc-50 dark:bg-zinc-800/50">
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
                            <tr class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 font-bold text-primary-600 dark:text-primary-400">{{ $booking->code }}</td>
                                <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">
                                    {{ $booking->court->name ?? 'N/A' }}
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $booking->court->venue->name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($booking->total_amount) }} VNĐ
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :variant="$booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger')">
                                        {{ ucfirst($booking->status) }}
                                    </x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state icon="heroicons-o-calendar"
                                                   title="Bạn chưa có đơn đặt sân nào"
                                                   description="Khi bạn đặt sân, lịch sử đơn sẽ xuất hiện ở đây."/>
                                </td>
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