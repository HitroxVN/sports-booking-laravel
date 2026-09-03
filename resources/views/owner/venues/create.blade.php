<x-owner-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ __('Thêm Khu Sân Mới') }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">

        <div class="card-base">
            <div class="px-8 py-6 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Thông tin cơ sở</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Điền đầy đủ thông tin để khách hàng dễ dàng tìm thấy bạn.</p>
            </div>

            <form action="{{ route('owner.venues.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf

                <!-- Thông tin cơ bản -->
                <div>
                    <h4 class="label-eyebrow mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-2">1. Thông tin chung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="venue-name" class="label-eyebrow block mb-2">Tên Khu Sân <span class="text-red-500">*</span></label>
                            <input id="venue-name" type="text" name="name" value="{{ old('name') }}" class="input-base" placeholder="VD: Sân bóng Mạch Tràng">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="venue-phone" class="label-eyebrow block mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                <input id="venue-phone" type="text" name="phone" value="{{ old('phone') }}" class="input-base" placeholder="VD: 0987...">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="venue-email" class="label-eyebrow block mb-2">Email</label>
                                <input id="venue-email" type="email" name="email" value="{{ old('email') }}" class="input-base" placeholder="contact@...">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vị trí -->
                <div>
                    <h4 class="label-eyebrow mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-2">2. Địa chỉ chi tiết</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="venue-city" class="label-eyebrow block mb-2">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                            <input id="venue-city" type="text" name="city" value="{{ old('city') }}" class="input-base" placeholder="VD: Hà Nội">
                            @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue-district" class="label-eyebrow block mb-2">Quận/Huyện <span class="text-red-500">*</span></label>
                            <input id="venue-district" type="text" name="district" value="{{ old('district') }}" class="input-base" placeholder="VD: Đông Anh">
                            @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="venue-ward" class="label-eyebrow block mb-2">Phường/Xã</label>
                            <input id="venue-ward" type="text" name="ward" value="{{ old('ward') }}" class="input-base">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="venue-address" class="label-eyebrow block mb-2">Số nhà, Tên đường <span class="text-red-500">*</span></label>
                        <input id="venue-address" type="text" name="address" value="{{ old('address') }}" class="input-base" placeholder="VD: Thôn Mạch Tràng...">
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="venue-latitude" class="label-eyebrow block mb-2">Vĩ độ (Latitude)</label>
                            <input id="venue-latitude" type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="input-base bg-zinc-50 dark:bg-zinc-900">
                        </div>
                        <div>
                            <label for="venue-longitude" class="label-eyebrow block mb-2">Kinh độ (Longitude)</label>
                            <input id="venue-longitude" type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="input-base bg-zinc-50 dark:bg-zinc-900">
                        </div>
                    </div>
                </div>

                <!-- Bổ sung -->
                <div>
                    <h4 class="label-eyebrow mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-2">3. Hình ảnh & Tiện ích</h4>

                    <div class="mb-6">
                        <label class="label-eyebrow block mb-3">Tiện ích tại sân</label>
                        <div class="flex flex-wrap gap-6 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-800">
                            @php $oldAmenities = old('amenities', []); @endphp
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="wifi" @checked(in_array('wifi', $oldAmenities)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                <span class="ml-2 font-medium text-zinc-700 dark:text-zinc-300">Wifi miễn phí</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="parking" @checked(in_array('parking', $oldAmenities)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                <span class="ml-2 font-medium text-zinc-700 dark:text-zinc-300">Bãi đỗ xe</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="canteen" @checked(in_array('canteen', $oldAmenities)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                <span class="ml-2 font-medium text-zinc-700 dark:text-zinc-300">Căng tin/Nước</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="changing_room" @checked(in_array('changing_room', $oldAmenities)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                <span class="ml-2 font-medium text-zinc-700 dark:text-zinc-300">Phòng thay đồ</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="label-eyebrow block mb-2">Mô tả giới thiệu</label>
                        <textarea name="description" rows="4" class="input-base" placeholder="Giới thiệu về chất lượng mặt sân, hệ thống chiếu sáng...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="label-eyebrow block mb-2">Ảnh bìa (Cover Image)</label>
                        <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 transition cursor-pointer border border-zinc-200 dark:border-zinc-700 rounded-xl">
                        @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                    <a href="{{ route('owner.venues.index') }}" class="btn-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn-primary">
                        Lưu Thông Tin Sân
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-owner-layout>
