<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Thêm Sân Con Mới') }} — {{ $venue->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.venues.courts.index', $venue->slug) }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách Sân Con
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <form action="{{ route('owner.venues.courts.store', $venue->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tên Sân Con *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="VD: Sân 1, Sân A, Sân VIP..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh Sân Con</label>
                        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-xl p-2 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Môn Thể Thao *</label>
                            <select name="sport_id" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                <option value="">-- Chọn môn thể thao --</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" @selected(old('sport_id') == $sport->id)>{{ $sport->name }}</option>
                                @endforeach
                            </select>
                            @error('sport_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Loại Mặt Sân *</label>
                            <select name="surface_type" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500">
                                <option value="artificial_turf">Cỏ nhân tạo</option>
                                <option value="natural_grass">Cỏ tự nhiên</option>
                                <option value="wood">Sàn gỗ (Trong nhà)</option>
                                <option value="concrete">Sân bê tông</option>
                            </select>
                            @error('surface_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số người tối đa</label>
                            <input type="number" name="max_players" value="{{ old('max_players') }}" placeholder="VD: 10, 14..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('max_players') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng Thái *</label>
                            <select name="status" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                <option value="active">Hoạt động</option>
                                <option value="maintenance">Bảo trì</option>
                                <option value="closed">Đóng cửa</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô Tả Thêm</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Lưu Sân Con
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>