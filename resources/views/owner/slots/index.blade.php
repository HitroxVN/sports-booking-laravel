<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <a href="{{ route('owner.venues.courts.index', $court->venue) }}" class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 mb-2 inline-block">
                    &larr; Quay lại danh sách Sân Con
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Quản lý Khung Giờ') }} - {{ $court->name }}
                </h1>
            </div>
            <a href="{{ route('owner.courts.slots.create', $court) }}" class="btn-primary shrink-0">
                Thêm Khung Giờ
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Ngày trong tuần</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Thời gian</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Giá thường</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Giờ vàng?</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Giá giờ vàng</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @php
                            $days = [0 => 'Chủ Nhật', 1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy'];
                        @endphp
                        @forelse($slots as $slot)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">{{ $days[$slot->day_of_week] ?? 'Tất cả các ngày / Chưa chọn' }}</td>
                            <td class="px-6 py-4 text-center font-mono text-zinc-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-700 dark:text-zinc-300">{{ number_format($slot->price, 0, ',', '.') }} đ</td>
                            <td class="px-6 py-4 text-center">
                                @if($slot->is_peak)
                                    <x-badge variant="warning">Giờ Vàng</x-badge>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-primary-600 dark:text-primary-400">
                                {{ $slot->is_peak && $slot->peak_price ? number_format($slot->peak_price, 0, ',', '.') . ' đ' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('owner.slots.destroy', $slot) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa khung giờ này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8">
                                <x-empty-state icon="⏰" title="Sân này chưa có khung giờ nào" description="Thiết lập khung giờ và giá để khách hàng có thể đặt sân." actionUrl="{{ route('owner.courts.slots.create', $court) }}" actionText="Thêm Khung Giờ" />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
