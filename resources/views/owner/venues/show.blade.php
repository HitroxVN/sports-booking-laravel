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

            {{-- Giờ hoạt động theo tuần --}}
            @php
                $dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
                // Map giờ hiện có theo day_of_week; ngày chưa có row = chưa cài (mặc định mở 06:00-22:00)
                $hoursByDay = $venue->operatingHours->keyBy('day_of_week');
            @endphp
            <div class="px-6 pb-6 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <h4 class="label-eyebrow mb-1">Giờ hoạt động theo tuần</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Khách đặt sân sẽ bị chặn ngoài các khung giờ này. Ngày chưa cài đặt được mặc định là mở 06:00 - 22:00.</p>
                <form method="POST" action="{{ route('owner.venues.operating-hours.update', $venue) }}" x-data>
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        @foreach($dayNames as $day => $name)
                            @php $h = $hoursByDay->get($day); @endphp
                            <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50"
                                 x-data="{ closed: {{ $h?->is_closed ? 'true' : ($h ? 'false' : 'false') }} }">
                                <label class="inline-flex items-center gap-2 w-32 font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                    <input type="hidden" name="hours[{{ $day }}][is_closed]" value="0">
                                    <input type="checkbox" name="hours[{{ $day }}][is_closed]" value="1"
                                           @checked($h?->is_closed) @change="closed = $event.target.checked"
                                           class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500">
                                    {{ $name }}
                                </label>
                                <template x-if="closed">
                                    <span class="text-sm text-zinc-400 dark:text-zinc-500 italic w-40">Nghỉ — không mở cửa</span>
                                </template>
                                <div x-show="!closed" class="flex items-center gap-2">
                                    <input type="time" name="hours[{{ $day }}][open_time]" value="{{ $h ? substr($h->open_time, 0, 5) : '06:00' }}"
                                           class="input-base w-auto text-sm" x-bind:required="!closed">
                                    <span class="text-zinc-400">–</span>
                                    <input type="time" name="hours[{{ $day }}][close_time]" value="{{ $h ? substr($h->close_time, 0, 5) : '22:00' }}"
                                           class="input-base w-auto text-sm" x-bind:required="!closed">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn-primary text-sm mt-4">Lưu giờ hoạt động</button>
                </form>
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
