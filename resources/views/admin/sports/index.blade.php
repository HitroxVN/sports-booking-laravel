<x-admin-layout :title="'Môn thể thao'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Môn thể thao</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quản lý danh mục các môn thể thao</p>
    </div>

    {{-- Form thêm mới --}}
    <div class="card-base p-6 mb-6">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Thêm môn thể thao</h3>
        <form method="POST" action="{{ route('admin.sports.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="w-40">
                <label class="label-eyebrow block mb-1">Ảnh (tùy chọn)</label>
                <input type="file" name="icon" accept="image/*"
                       class="w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-3 file:px-3 file:py-2 file:text-xs file:font-semibold file:rounded-xl file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 cursor-pointer">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label for="sport-name" class="label-eyebrow block mb-1">Tên môn</label>
                <input id="sport-name" type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       placeholder="VD: Bóng đá"
                       class="input-base @error('name', 'default') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-primary">+ Thêm</button>
        </form>
    </div>

    {{-- Bảng --}}
    <div class="card-base">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold w-16">Icon</th>
                        <th class="p-4 font-semibold">Tên</th>
                        <th class="p-4 font-semibold text-center">Số sân</th>
                        <th class="p-4 font-semibold text-center">Trạng thái</th>
                        <th class="p-4 font-semibold text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($sports as $sport)
                    <tr x-data="{ editing: @error('name', 'sport_' . $sport->id) true @else false @enderror }" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="p-4 text-center">
                            @if ($sport->icon && \Storage::disk('public')->exists($sport->icon))
                                <img src="{{ asset('storage/' . $sport->icon) }}" alt="{{ $sport->name }}"
                                     class="w-12 h-12 object-cover rounded-xl inline-block">
                            @else
                                <span class="text-2xl">🏅</span>
                            @endif
                        </td>
                        <td class="p-4">
                            {{-- Hiển thị tên --}}
                            <span x-show="!editing" class="font-semibold text-zinc-900 dark:text-zinc-50">{{ $sport->name }}</span>

                            {{-- Inline edit form --}}
                            <form x-show="editing" x-cloak method="POST" action="{{ route('admin.sports.update', $sport) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="file" name="icon" accept="image/*" title="Chọn ảnh mới (bỏ trống = giữ ảnh cũ)"
                                       class="w-32 text-xs text-zinc-600 dark:text-zinc-300 file:mr-2 file:px-2 file:py-1.5 file:text-[11px] file:font-semibold file:rounded-lg file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 cursor-pointer">
                                <input type="text" name="name" value="{{ $sport->name }}" required maxlength="100"
                                       class="flex-1 min-w-[150px] input-base @error('name', 'sport_' . $sport->id) border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                                <button type="submit" class="btn-primary px-3 py-1.5 text-xs">Lưu</button>
                                <button type="button" @click="editing = false" class="btn-secondary px-3 py-1.5 text-xs">Hủy</button>
                            </form>

                            {{-- Lỗi validate của riêng row này --}}
                            @error('name', 'sport_' . $sport->id)
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400" x-show="editing">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="p-4 text-center text-zinc-600 dark:text-zinc-300 text-sm">{{ $sport->courts_count }}</td>
                        <td class="p-4 text-center">
                            <form method="POST" action="{{ route('admin.sports.update', $sport) }}" class="inline"
                                  @submit="submitting = true" x-data="{ submitting: false }">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="name" value="{{ $sport->name }}">
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" name="is_active" value="{{ $sport->is_active ? 0 : 1 }}"
                                        :disabled="submitting"
                                        class="px-3 py-1.5 rounded-full text-xs font-semibold transition
                                               {{ $sport->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ $sport->is_active ? 'Hoạt động' : 'Tắt' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="button" @click="editing = true" x-show="!editing"
                                    class="px-3 py-1.5 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/50 rounded-xl text-xs font-semibold transition">Sửa</button>
                            <form method="POST" action="{{ route('admin.sports.destroy', $sport) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" x-data=""
                                        @click="if(!confirm(`Chắc chắn xóa môn thể thao ${@js($sport->name)}?`)) $event.preventDefault()"
                                        class="px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl text-xs font-semibold transition">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4">
                            <x-empty-state icon="heroicons-o-calendar" title="Chưa có môn thể thao nào"
                                           description="Thêm môn thể thao đầu tiên bằng biểu mẫu phía trên." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
