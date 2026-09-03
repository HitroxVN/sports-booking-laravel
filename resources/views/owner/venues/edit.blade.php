<x-owner-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách Khu Sân
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ __('Chỉnh Sửa Khu Sân') }} - {{ $venue->name }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">

        <div class="card-base">
            <div class="p-8">
                <!-- Hiển thị lỗi validation nếu có -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm">
                        <strong class="font-bold">Vui lòng kiểm tra lại các trường thông tin:</strong>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('owner.venues.update', $venue) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="venue-name" class="label-eyebrow block mb-2">Tên Khu Sân *</label>
                        <input id="venue-name" type="text" name="name" value="{{ old('name', $venue->name) }}" required placeholder="VD: Sân Thể Thao Thủ Đức..." class="input-base">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bổ sung ô chọn ảnh đại diện khu sân -->
                    <div>
                        <label class="label-eyebrow block mb-2">Ảnh Đại Diện Khu Sân Mới</label>
                        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Cụm Địa Chỉ 3 cột: Số nhà, Quận/Huyện, Tỉnh/Thành phố -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="venue-address" class="label-eyebrow block mb-2">Số nhà, Đường *</label>
                            <input id="venue-address" type="text" name="address" value="{{ old('address', $venue->address) }}" required placeholder="VD: Số 1 Võ Văn Ngân" class="input-base">
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue-district" class="label-eyebrow block mb-2">Quận/Huyện *</label>
                            <input id="venue-district" type="text" name="district" value="{{ old('district', $venue->district) }}" required placeholder="VD: TP Thủ Đức" class="input-base">
                            @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue-city" class="label-eyebrow block mb-2">Tỉnh/Thành phố *</label>
                            <input id="venue-city" type="text" name="city" value="{{ old('city', $venue->city) }}" required placeholder="VD: TP.HCM" class="input-base">
                            @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="venue-phone" class="label-eyebrow block mb-2">Số Điện Thoại Liên Hệ *</label>
                            <input id="venue-phone" type="text" name="phone" value="{{ old('phone', $venue->phone) }}" required placeholder="VD: 0901234567" class="input-base">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="venue-status" class="label-eyebrow block mb-2">Trạng Thái *</label>
                            <select id="venue-status" name="status" class="input-base">
                                <option value="active" @selected(old('status', $venue->status) == 'active')>Hoạt động</option>
                                <option value="pending" @selected(old('status', $venue->status) == 'pending')>Chờ duyệt</option>
                                <option value="closed" @selected(old('status', $venue->status) == 'closed')>Đóng cửa</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-eyebrow block mb-2">Mô Tả / Giới Thiệu</label>
                        <textarea name="description" rows="4" placeholder="Mô tả về tiện ích, không gian sân bãi..." class="input-base">{{ old('description', $venue->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.venues.index') }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary">
                            Cập Nhật Khu Sân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-owner-layout>
