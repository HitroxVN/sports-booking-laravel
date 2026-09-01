<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Chi Tiết Khu Sân') }} - {{ $venue->name }}
                </h1>
            </div>
            <a href="{{ route('owner.venues.index') }}" class="btn-secondary text-xs shrink-0">
                &larr; Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div class="card-base">

            <!-- Khung hiển thị ảnh đại diện của Khu Sân từ bảng venue_images -->
            <div class="p-6 pb-0">
                <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 h-[280px]">
                @if($venue->images && $venue->images->count() > 0)
                @php
                    $img = $venue->images->first();
                @endphp
                <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $venue->name }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                <div class="flex flex-col items-center justify-center h-full text-zinc-400 dark:text-zinc-500">
                    <svg class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium">Chưa có hình ảnh khu sân</span>
                </div>
                @endif
                </div>
            </div>

            <div class="px-6 pb-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mb-4">{{ $venue->name }}</h3>

                    <div class="space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <p><strong class="text-zinc-900 dark:text-zinc-100">Địa chỉ:</strong> {{ $venue->address }} {{ $venue->district ? '- ' . $venue->district : '' }} {{ $venue->city ? '(' . $venue->city . ')' : '' }}</p>
                        <p><strong class="text-zinc-900 dark:text-zinc-100">Điện thoại:</strong> {{ $venue->phone ?? 'Chưa cập nhật' }}</p>
                        <p><strong class="text-zinc-900 dark:text-zinc-100">Email:</strong> {{ $venue->email ?? 'Chưa cập nhật' }}</p>
                        <p class="flex items-center">
                            <strong class="mr-2 text-zinc-900 dark:text-zinc-100">Trạng thái:</strong>
                            @php
                                $statusVariants = match($venue->status) {
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    default => 'danger',
                                };
                                $statusLabels = [
                                    'active' => 'Hoạt động',
                                    'pending' => 'Chờ duyệt',
                                    'closed' => 'Đóng cửa'
                                ];
                            @endphp
                            <x-badge :variant="$statusVariants">
                                {{ $statusLabels[$venue->status] ?? $venue->status }}
                            </x-badge>
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="label-eyebrow mb-2">Mô tả khu sân</h4>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 min-h-[120px] leading-relaxed">
                        {{ $venue->description ?? 'Không có mô tả chi tiết cho khu sân này.' }}
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap gap-3">
                <a href="{{ route('owner.venues.edit', $venue) }}" class="btn-primary text-sm">
                    Chỉnh sửa thông tin
                </a>
                <a href="{{ route('owner.venues.courts.index', $venue) }}" class="btn-secondary text-sm">
                    Quản lý Sân con bên trong
                </a>
                <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="btn-ghost text-sm">
                    Quản lý Khuyến mãi
                </a>
            </div>
        </div>

    </div>
</x-owner-layout>
