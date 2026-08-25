<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Thêm Khu Sân Mới') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại danh sách
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-pink-50 bg-white">
                    <h3 class="text-xl font-extrabold text-gray-800">Thông tin cơ sở</h3>
                    <p class="text-sm text-gray-500 mt-1">Điền đầy đủ thông tin để khách hàng dễ dàng tìm thấy bạn.</p>
                </div>

                <form action="{{ route('owner.venues.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                    @csrf
                    
                    <!-- Thông tin cơ bản -->
                    <div>
                        <h4 class="text-sm font-bold text-pink-500 uppercase tracking-wider mb-4 border-b border-pink-100 pb-2">1. Thông tin chung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tên Khu Sân <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="VD: Sân bóng Mạch Tràng">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="VD: 0987...">
                                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="contact@...">
                                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vị trí -->
                    <div>
                        <h4 class="text-sm font-bold text-pink-500 uppercase tracking-wider mb-4 border-b border-pink-100 pb-2">2. Địa chỉ chi tiết</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                                <input type="text" name="city" value="{{ old('city') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="VD: Hà Nội">
                                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Quận/Huyện <span class="text-red-500">*</span></label>
                                <input type="text" name="district" value="{{ old('district') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="VD: Đông Anh">
                                @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phường/Xã</label>
                                <input type="text" name="ward" value="{{ old('ward') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số nhà, Tên đường <span class="text-red-500">*</span></label>
                            <input type="text" name="address" value="{{ old('address') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="VD: Thôn Mạch Tràng...">
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Vĩ độ (Latitude)</label>
                                <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kinh độ (Longitude)</label>
                                <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <!-- Bổ sung -->
                    <div>
                        <h4 class="text-sm font-bold text-pink-500 uppercase tracking-wider mb-4 border-b border-pink-100 pb-2">3. Hình ảnh & Tiện ích</h4>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Tiện ích tại sân</label>
                            <div class="flex flex-wrap gap-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                @php $oldAmenities = old('amenities', []); @endphp
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="wifi" @checked(in_array('wifi', $oldAmenities)) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                    <span class="ml-2 font-medium text-gray-700">Wifi miễn phí</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="parking" @checked(in_array('parking', $oldAmenities)) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                    <span class="ml-2 font-medium text-gray-700">Bãi đỗ xe</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="canteen" @checked(in_array('canteen', $oldAmenities)) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                    <span class="ml-2 font-medium text-gray-700">Căng tin/Nước</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="changing_room" @checked(in_array('changing_room', $oldAmenities)) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                    <span class="ml-2 font-medium text-gray-700">Phòng thay đồ</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả giới thiệu</label>
                            <textarea name="description" rows="4" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm transition" placeholder="Giới thiệu về chất lượng mặt sân, hệ thống chiếu sáng...">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh bìa (Cover Image)</label>
                            <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-pink-50 file:text-pink-600 hover:file:bg-pink-100 transition cursor-pointer border border-gray-200 rounded-xl">
                            @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100 flex justify-end">
                        <a href="{{ route('owner.venues.index') }}" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-semibold mr-4 transition">Hủy bỏ</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                            Lưu Thông Tin Sân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>