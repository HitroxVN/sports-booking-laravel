<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Quản lý Đơn Đặt Sân') }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Theo dõi và quản lý các lượt khách đặt sân</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" class="btn-secondary text-xs shrink-0">
                &larr; Trở về trang chủ
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <!-- Bộ lọc trạng thái -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('owner.bookings.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ !request('status') ? 'bg-primary-600 text-white shadow-sm' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Tất cả</a>
            <a href="{{ route('owner.bookings.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Chờ xử lý</a>
            <a href="{{ route('owner.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ request('status') == 'confirmed' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Đã xác nhận</a>
        </div>

        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Mã Đơn / Ngày Đặt</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Khách Hàng</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Thông Tin Sân</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tổng Tiền</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Trạng Thái</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-primary-600 dark:text-primary-400">#{{ $booking->code }}</div>
                                <!-- Đã fix Lỗi 10: Dùng toán tử ?-> để an toàn khi created_at bị null -->
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $booking->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->user->name ?? 'Khách lẻ' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $booking->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-700 dark:text-zinc-300">{{ $booking->court->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} |
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($booking->total_amount, 0, ',', '.') }} đ
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($booking->isPending())
                                    <x-badge variant="warning">Chờ xử lý</x-badge>
                                @elseif($booking->isConfirmed())
                                    <x-badge variant="info">Đã xác nhận</x-badge>
                                @elseif($booking->isCompleted())
                                    <x-badge variant="success">Hoàn thành</x-badge>
                                @elseif($booking->isCancelled())
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
                                <x-empty-state icon="📅" title="Chưa có đơn đặt sân nào" description="Các đơn đặt sân của khách hàng sẽ xuất hiện tại đây." />
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
</x-owner-layout>
