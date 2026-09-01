<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <a href="{{ route('owner.venues.index') }}" class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 mb-2 inline-block">
                    &larr; Quay lại danh sách Khu sân
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    Quản lý Khuyến Mãi - {{ $venue->name }}
                </h1>
            </div>
            <a href="{{ route('owner.venues.promotions.create', $venue) }}" class="btn-primary shrink-0">
                Tạo Mã Mới
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Mã Code</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Mức Giảm</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Đã Dùng / Tối Đa</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Hạn Sử Dụng</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-center">Trạng Thái</th>
                            <th class="px-6 py-3 font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($promotions as $promo)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-primary-600 dark:text-primary-400 text-base">{{ $promo->code }}</td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $promo->discount_type === 'percent' ? number_format($promo->discount_value, 0) . '%' : number_format($promo->discount_value, 0, ',', '.') . ' đ' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $promo->used_count }}</span> / {{ $promo->max_uses ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 text-sm">
                                {{ \Carbon\Carbon::parse($promo->starts_at)->format('d/m/Y') }}<br>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">đến</span> {{ \Carbon\Carbon::parse($promo->expires_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($promo->isValid())
                                    <x-badge variant="success">Đang chạy</x-badge>
                                @else
                                    <x-badge variant="default">Vô hiệu</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <!-- Nút Sửa -->
                                    <a href="{{ route('owner.promotions.edit', $promo) }}" class="btn-ghost text-xs">
                                        Sửa
                                    </a>

                                    <!-- Nút Xóa -->
                                    <form action="{{ route('owner.promotions.destroy', $promo) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa mã giảm giá này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8">
                                <x-empty-state icon="🎟️" title="Khu sân này chưa có mã khuyến mãi nào" description="Tạo mã giảm giá để thu hút thêm khách hàng." actionUrl="{{ route('owner.venues.promotions.create', $venue) }}" actionText="Tạo Mã Mới" />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-layout>
