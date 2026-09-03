<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Quản lý Sân Con') }} - {{ $venue->name }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quản lý các sân thể thao thuộc khu vực này</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('owner.venues.index') }}" class="btn-secondary text-xs">
                    &larr; Trở về Khu Sân
                </a>
                <a href="{{ route('owner.venues.courts.create', $venue) }}" class="btn-primary">
                    Thêm sân con mới
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Ảnh</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tên Sân Con</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Môn Thể Thao</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Loại Mặt Sân</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Trạng Thái</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                        @forelse($courts as $court)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    @if(!empty($court->image))
                                        <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="w-14 h-14 object-cover rounded-xl border border-zinc-200 dark:border-zinc-700 mx-auto">
                                    @else
                                        <div class="w-14 h-14 bg-zinc-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-xs font-semibold mx-auto border border-zinc-200 dark:border-zinc-700">
                                            No img
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $court->name }}
                                    <div class="text-xs font-normal text-zinc-500 dark:text-zinc-400">Tối đa: {{ $court->max_players ?? 'N/A' }} người</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    {{ $court->sport->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="inline-block px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-full text-xs font-semibold border border-zinc-200 dark:border-zinc-700">
                                        {{ $court->surface_type_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($court->status === 'active')
                                        <x-badge variant="success">{{ $court->status_name }}</x-badge>
                                    @elseif($court->status === 'maintenance')
                                        <x-badge variant="warning">{{ $court->status_name }}</x-badge>
                                    @else
                                        <x-badge variant="danger">{{ $court->status_name }}</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('owner.courts.slots.index', $court) }}" class="btn-ghost text-xs">
                                            Khung giờ & Giá
                                        </a>
                                        <a href="{{ route('owner.courts.closures.index', $court) }}" class="btn-ghost text-xs">
                                            Khóa lịch
                                        </a>
                                        <a href="{{ route('owner.courts.edit', $court) }}" class="btn-ghost text-xs">
                                            Sửa
                                        </a>
                                        <form action="{{ route('owner.courts.destroy', $court) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này không? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8">
                                        <x-empty-state icon="⚽" title="Khu sân này chưa có sân con nào" description="Thêm sân con đầu tiên để bắt đầu bán khung giờ." actionUrl="{{ route('owner.venues.courts.create', $venue) }}" actionText="Thêm sân con mới" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($courts->hasPages())
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        {{ $courts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-owner-layout>
