<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Quản lý Khu Sân') }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quản lý tất cả cơ sở và sân thể thao của bạn tại đây</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('owner.dashboard') }}" class="btn-secondary text-xs">
                    &larr; Trở về trang chủ
                </a>
                <a href="{{ route('owner.venues.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Thêm khu sân
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <!-- Bảng dữ liệu -->
        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tên Sân & Mã</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Vị trí</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Liên hệ</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Trạng thái</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($venues as $venue)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $venue->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-mono">ID: #{{ $venue->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-zinc-700 dark:text-zinc-300 text-sm truncate max-w-xs">{{ $venue->address }}</div>
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">{{ $venue->district }}, {{ $venue->city }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-zinc-700 dark:text-zinc-300 text-sm font-medium">{{ $venue->phone }}</div>
                                <div class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">{{ $venue->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($venue->status === 'active')
                                    <x-badge variant="success">Hoạt động</x-badge>
                                @elseif($venue->status === 'pending')
                                    <x-badge variant="warning">Chờ duyệt</x-badge>
                                @else
                                    <x-badge variant="default">Đóng cửa</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <!-- Nút quản lý Sân Con -->
                                    <a href="{{ route('owner.venues.courts.index', $venue) }}" class="btn-ghost text-xs">Sân con ({{ $venue->courts_count }})</a>

                                    <!-- Nút quản lý Khuyến Mãi -->
                                    <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="btn-ghost text-xs">Khuyến mãi</a>

                                    <!-- Nút Xem Chi Tiết -->
                                    <a href="{{ route('owner.venues.show', $venue) }}" class="btn-ghost text-xs">Chi tiết</a>

                                    <a href="{{ route('owner.venues.edit', $venue) }}" class="btn-ghost text-xs">Sửa</a>

                                    <form action="{{ route('owner.venues.destroy', $venue) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8">
                                <x-empty-state icon="🏟️" title="Bạn chưa có khu sân nào" description='Hãy bấm "Thêm khu sân" để bắt đầu thiết lập cơ sở của bạn.' actionUrl="{{ route('owner.venues.create') }}" actionText="Thêm khu sân" />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $venues->links() }}
        </div>
    </div>
</x-owner-layout>
