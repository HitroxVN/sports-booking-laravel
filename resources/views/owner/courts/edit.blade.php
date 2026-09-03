<x-owner-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.venues.courts.index', $court->venue->slug) }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách Sân Con
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ __('Chỉnh Sửa Sân Con') }} - {{ $court->venue->name }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="p-8">
                <form action="{{ route('owner.courts.update', $court) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="court-name" class="label-eyebrow block mb-2">Tên Sân Con *</label>
                        <input id="court-name" type="text" name="name" value="{{ old('name', $court->name) }}" required placeholder="VD: Sân 1, Sân A, Sân VIP..." class="input-base">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-eyebrow block mb-2">Ảnh Sân Con Mới</label>
                        @if($court->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="w-24 h-24 object-cover rounded-xl border border-zinc-200 dark:border-zinc-700">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 block mt-1">Ảnh hiện tại</span>
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="court-sport" class="label-eyebrow block mb-2">Môn Thể Thao *</label>
                            <select id="court-sport" name="sport_id" required class="input-base">
                                <option value="">-- Chọn môn thể thao --</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" @selected(old('sport_id', $court->sport_id) == $sport->id)>{{ $sport->name }}</option>
                                @endforeach
                            </select>
                            @error('sport_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="court-surface" class="label-eyebrow block mb-2">Loại Mặt Sân *</label>
                            <select id="court-surface" name="surface_type" required class="input-base">
                                <option value="artificial_turf" @selected(old('surface_type', $court->surface_type) == 'artificial_turf')>Cỏ nhân tạo</option>
                                <option value="natural_grass" @selected(old('surface_type', $court->surface_type) == 'natural_grass')>Cỏ tự nhiên</option>
                                <option value="wood" @selected(old('surface_type', $court->surface_type) == 'wood')>Sàn gỗ</option>
                                <option value="concrete" @selected(old('surface_type', $court->surface_type) == 'concrete')>Bê tông / Thảm cao su</option>
                            </select>
                            @error('surface_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="court-max-players" class="label-eyebrow block mb-2">Số người tối đa</label>
                            <input id="court-max-players" type="number" name="max_players" value="{{ old('max_players', $court->max_players) }}" placeholder="VD: 10, 14..." class="input-base">
                            @error('max_players') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="court-status" class="label-eyebrow block mb-2">Trạng Thái *</label>
                            <select id="court-status" name="status" class="input-base">
                                <option value="active" @selected(old('status', $court->status) == 'active')>Hoạt động</option>
                                <option value="maintenance" @selected(old('status', $court->status) == 'maintenance')>Bảo trì</option>
                                <option value="closed" @selected(old('status', $court->status) == 'closed')>Đóng cửa</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-eyebrow block mb-2">Mô Tả Thêm</label>
                        <textarea name="description" rows="3" class="input-base">{{ old('description', $court->description) }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.venues.courts.index', $court->venue->slug) }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary">
                            Cập Nhật Thay Đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-owner-layout>
