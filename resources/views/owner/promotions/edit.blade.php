<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Chỉnh Sửa Khuyến Mãi') }} — {{ $promotion->code }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('owner.venues.promotions.index', $promotion->venue_id) }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold transition-colors">
                    &larr; Quay lại danh sách Khuyến Mãi
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <form action="{{ route('owner.promotions.update', $promotion) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mã Khuyến Mãi *</label>
                        <input type="text" name="code" value="{{ old('code', $promotion->code) }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm uppercase">
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả</label>
                        <input type="text" name="description" value="{{ old('description', $promotion->description) }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Loại Giảm Giá *</label>
                            <select name="discount_type" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                <option value="percent" @selected(old('discount_type', $promotion->discount_type) === 'percent')>Phần trăm (%)</option>
                                <option value="fixed" @selected(old('discount_type', $promotion->discount_type) === 'fixed')>Số tiền cố định (VNĐ)</option>
                            </select>
                            @error('discount_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giá Trị Giảm *</label>
                            <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" step="0.01" name="min_amount" value="{{ old('min_amount', $promotion->min_amount) }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('min_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giới hạn số lần dùng</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $promotion->max_uses) }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('max_uses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Thời gian bắt đầu *</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d\TH:i')) }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('starts_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Thời gian kết thúc *</label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($promotion->expires_at)->format('Y-m-d\TH:i')) }}" required class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng Thái Kích Hoạt</label>
                        <div class="flex items-center mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active)) class="rounded border-gray-300 text-pink-600 shadow-sm focus:ring-pink-500 w-5 h-5">
                            <span class="ml-2 text-sm text-gray-600 font-medium">Đang cho phép sử dụng</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Cập Nhật Mã
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>