<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Tạo Mã Khuyến Mãi — {{ $venue->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <form action="{{ route('owner.venues.promotions.store', $venue) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mã Code (Chữ in hoa) <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="VD: SUMMER28" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm uppercase">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng Thái Kích Hoạt</label>
                            <label class="inline-flex items-center mt-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                                <span class="ml-2 font-bold text-gray-700">Đang bật</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả chương trình</label>
                        <input type="text" name="description" value="{{ old('description') }}" placeholder="VD: Khuyến mãi cho anh em FC Mạch Tràng nhân dịp giải đấu..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Loại Giảm Giá <span class="text-red-500">*</span></label>
                            <select name="discount_type" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                <option value="percent">Giảm theo phần trăm (%)</option>
                                <option value="fixed">Giảm số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mức Giảm <span class="text-red-500">*</span></label>
                            <input type="number" name="discount_value" value="{{ old('discount_value') }}" required min="0" placeholder="VD: 10 hoặc 50000" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Đơn Tối Thiểu Được Áp Dụng (VNĐ)</label>
                            <input type="number" name="min_amount" value="{{ old('min_amount') }}" min="0" placeholder="VD: 200000 (Để trống nếu không giới hạn)" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giới hạn số lần dùng</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="VD: 100 (Để trống nếu không giới hạn)" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày bắt đầu <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày kết thúc <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Tạo Khuyến Mãi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>