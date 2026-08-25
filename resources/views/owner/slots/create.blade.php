<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Thiết lập Khung Giờ — {{ $court->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.courts.slots.index', $court) }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <form action="{{ route('owner.courts.slots.store', $court) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày áp dụng <span class="text-red-500">*</span></label>
                        <select name="day_of_week" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            <option value="1">Thứ Hai</option>
                            <option value="2">Thứ Ba</option>
                            <option value="3">Thứ Tư</option>
                            <option value="4">Thứ Năm</option>
                            <option value="5">Thứ Sáu</option>
                            <option value="6">Thứ Bảy</option>
                            <option value="0">Chủ Nhật</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giờ bắt đầu <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giờ kết thúc <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 items-start">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giá thường (VNĐ) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" required min="0" placeholder="VD: 300000" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{ isPeak: false }">
                            <div class="mb-2 mt-1">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="is_peak" value="0">
                                    <input type="checkbox" name="is_peak" value="1" x-model="isPeak" class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                    <span class="ml-2 font-bold text-yellow-600">Là Giờ Vàng?</span>
                                </label>
                            </div>
                            
                            <div x-show="isPeak" x-transition>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Giá giờ vàng (VNĐ) <span class="text-red-500">*</span></label>
                                <input type="number" name="peak_price" min="0" placeholder="VD: 500000" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                @error('peak_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Lưu Khung Giờ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>