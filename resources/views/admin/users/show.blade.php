<x-admin-layout :title="'Chi tiết người dùng'">

    {{-- Page header --}}
    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Chi tiết người dùng</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Thông tin và đơn hàng của {{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">← Danh sách</a>
    </div>

    {{-- Thông tin user --}}
    <div class="card-base p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <span class="flex items-center justify-center w-16 h-16 rounded-full bg-primary-600 text-white text-xl font-bold shrink-0">
                {{ mb_substr(trim($user->name), 0, 1) }}
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ $user->name }}</h2>
                    @if($user->isOwner())
                        <x-badge variant="info">Chủ sân</x-badge>
                    @else
                        <x-badge variant="default">Khách hàng</x-badge>
                    @endif
                    @if($user->isActive())
                        <x-badge variant="success">Hoạt động</x-badge>
                    @else
                        <x-badge variant="danger">Bị khóa</x-badge>
                    @endif
                </div>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $user->email }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->phone ?? 'Chưa có SĐT' }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary px-3 py-2 text-xs">Sửa</a>
                @if($user->isActive())
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline">
                        @csrf
                        <button type="submit"
                                @click="if(!confirm(`Chắc chắn khóa tài khoản ${@js($user->name)}?`)) $event.preventDefault()"
                                class="px-3 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl text-xs font-semibold transition"
                                x-data="">Khóa</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl text-xs font-semibold transition">Mở khóa</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            @click="if(!confirm(`Chắc chắn xóa tài khoản ${@js($user->name)}? Hành động này không thể hoàn tác.`)) $event.preventDefault()"
                            class="px-3 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl text-xs font-semibold transition"
                            x-data="">Xóa</button>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
            <div>
                <p class="label-eyebrow">Ngày tạo</p>
                <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-50">{{ $user->created_at?->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="label-eyebrow">Email xác thực</p>
                <p class="mt-1 text-sm font-semibold {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực' }}
                </p>
            </div>
            <div>
                <p class="label-eyebrow">Số đơn hàng</p>
                <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-50">{{ $user->bookings_count }}</p>
            </div>
            @if($user->isOwner())
            <div>
                <p class="label-eyebrow">Số khu sân</p>
                <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-50">{{ $user->venues_count }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Đơn hàng của user --}}
    <div class="card-base">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Đơn hàng của user</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Mã đơn</th>
                        <th class="p-4 font-semibold">Khu sân › Sân con</th>
                        <th class="p-4 font-semibold">Ngày đặt</th>
                        <th class="p-4 font-semibold">Giờ</th>
                        <th class="p-4 font-semibold text-right">Tổng tiền</th>
                        <th class="p-4 font-semibold">Trạng thái</th>
                        <th class="p-4 font-semibold">Thanh toán</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="p-4 font-bold text-primary-600 dark:text-primary-400">#{{ $booking->code }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ $booking->court->venue->name ?? '—' }} › {{ $booking->court->name ?? '—' }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                        <td class="p-4 text-zinc-900 dark:text-zinc-50 font-semibold text-right">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</td>
                        <td class="p-4">
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
                        <td class="p-4">
                            @if($booking->isPaid())
                                <x-badge variant="success">Đã thanh toán</x-badge>
                            @elseif($booking->hasDeposit())
                                <x-badge variant="warning">Đã cọc</x-badge>
                            @else
                                <x-badge variant="default">Chưa thanh toán</x-badge>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-4">
                            <x-empty-state icon="heroicons-o-calendar" title="Chưa có đơn hàng nào"
                                           description="Người dùng này chưa đặt sân lần nào." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
