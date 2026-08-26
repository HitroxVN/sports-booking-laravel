<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Chỉnh Sửa Khu Sân') }} — {{ $venue->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách Khu Sân
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <!-- Hiển thị lỗi validation nếu có -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tên Khu Sân *</label>
                        <input type="text" name="name" value="{{ old('name', $venue->name) }}" required placeholder="VD: Sân Thể Thao Thủ Đức..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bổ sung ô chọn ảnh đại diện khu sân -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh Đại Diện Khu Sân Mới</label>
                        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-xl p-2 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Cụm Địa Chỉ 3 cột: Số nhà, Quận/Huyện, Tỉnh/Thành phố -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số nhà, Đường *</label>
                            <input type="text" name="address" value="{{ old('address', $venue->address) }}" required placeholder="VD: Số 1 Võ Văn Ngân" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quận/Huyện *</label>
                            <input type="text" name="district" value="{{ old('district', $venue->district) }}" required placeholder="VD: TP Thủ Đức" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh/Thành phố *</label>
                            <input type="text" name="city" value="{{ old('city', $venue->city) }}" required placeholder="VD: TP.HCM" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số Điện Thoại Liên Hệ *</label>
                            <input type="text" name="phone" value="{{ old('phone', $venue->phone) }}" required placeholder="VD: 0901234567" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng Thái *</label>
                            <select name="status" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                <option value="active" @selected(old('status', $venue->status) == 'active')>Hoạt động</option>
                                <option value="pending" @selected(old('status', $venue->status) == 'pending')>Chờ duyệt</option>
                                <option value="closed" @selected(old('status', $venue->status) == 'closed')>Đóng cửa</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô Tả / Giới Thiệu</label>
                        <textarea name="description" rows="4" placeholder="Mô tả về tiện ích, không gian sân bãi..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">{{ old('description', $venue->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Cập Nhật Khu Sân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>