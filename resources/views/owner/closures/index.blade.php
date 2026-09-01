<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <a href="{{ route('owner.venues.courts.index', $court->venue) }}" class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 mb-2 inline-block">
                    &larr; Quay lại danh sách Sân Con
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Quản lý Khóa Lịch') }} - {{ $court->name }}
                </h1>
            </div>
            <a href="{{ route('owner.courts.closures.create', $court) }}" class="btn-danger shrink-0">
                Thêm Lịch Khóa
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Ngày Khóa</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Khung Giờ</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Lý Do</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($closures as $closure)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($closure->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($closure->start_time && $closure->end_time)
                                    <span class="bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-2 py-1 rounded-xl text-xs font-mono">
                                        {{ \Carbon\Carbon::parse($closure->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($closure->end_time)->format('H:i') }}
                                    </span>
                                @else
                                    <x-badge variant="danger">Khóa cả ngày</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 italic">{{ $closure->reason }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('owner.closures.destroy', $closure) }}" method="POST" class="inline-block" onsubmit="return confirm('Mở lại lịch cho ngày này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/30">Mở lại sân</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8">
                                <x-empty-state icon="🔒" title="Sân này hiện không có lịch khóa đột xuất nào" description="Khóa lịch khi cần bảo trì hoặc cho thuê mục đích khác." actionUrl="{{ route('owner.courts.closures.create', $court) }}" actionText="Thêm Lịch Khóa" />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $closures->links() }}
        </div>
    </div>
</x-owner-layout>
