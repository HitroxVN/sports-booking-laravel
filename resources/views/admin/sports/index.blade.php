<x-admin-layout :title="'Môn thể thao'">

    {{-- Form thêm mới --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Thêm môn thể thao</h3>
        <form method="POST" action="{{ route('admin.sports.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="w-40">
                <label class="block text-xs font-bold text-gray-500 mb-1">Ảnh (tùy chọn)</label>
                <input type="file" name="icon" accept="image/*"
                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tên môn</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       placeholder="VD: Bóng đá"
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 @error('name', 'default') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">+ Thêm</button>
        </form>
    </div>

    {{-- Bảng --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold w-16">Icon</th>
                        <th class="p-4 font-semibold">Tên</th>
                        <th class="p-4 font-semibold text-center">Số sân</th>
                        <th class="p-4 font-semibold text-center">Trạng thái</th>
                        <th class="p-4 font-semibold text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sports as $sport)
                    <tr x-data="{ editing: @error('name', 'sport_' . $sport->id) true @else false @enderror }" class="hover:bg-gray-50 transition-colors">
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
                            <span x-show="!editing" class="font-semibold text-gray-800">{{ $sport->name }}</span>

                            {{-- Inline edit form --}}
                            <form x-show="editing" x-cloak method="POST" action="{{ route('admin.sports.update', $sport) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="file" name="icon" accept="image/*" title="Chọn ảnh mới (bỏ trống = giữ ảnh cũ)"
                                       class="w-32 text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="name" value="{{ $sport->name }}" required maxlength="100"
                                       class="flex-1 min-w-[150px] rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 @error('name', 'sport_' . $sport->id) border-red-300 @enderror">
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700">Lưu</button>
                                <button type="button" @click="editing = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200">Hủy</button>
                            </form>

                            {{-- Lỗi validate của riêng row này --}}
                            @error('name', 'sport_' . $sport->id)
                                <p class="mt-1 text-xs text-red-600" x-show="editing">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="p-4 text-center text-gray-600 text-sm">{{ $sport->courts_count }}</td>
                        <td class="p-4 text-center">
                            <form method="POST" action="{{ route('admin.sports.update', $sport) }}" class="inline"
                                  @submit="submitting = true" x-data="{ submitting: false }">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="name" value="{{ $sport->name }}">
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" name="is_active" value="{{ $sport->is_active ? 0 : 1 }}"
                                        :disabled="submitting"
                                        class="px-3 py-1.5 rounded-full text-xs font-bold transition
                                               {{ $sport->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ $sport->is_active ? 'Hoạt động' : 'Tắt' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="button" @click="editing = true" x-show="!editing"
                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-bold transition">Sửa</button>
                            <form method="POST" action="{{ route('admin.sports.destroy', $sport) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" x-data=""
                                        @click="if(!confirm(`Chắc chắn xóa môn thể thao ${@js($sport->name)}?`)) $event.preventDefault()"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400">Chưa có môn thể thao nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
