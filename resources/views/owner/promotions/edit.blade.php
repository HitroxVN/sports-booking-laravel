<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.venues.promotions.index', $promotion->venue_id) }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách Khuyến Mãi
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ __('Chỉnh Sửa Khuyến Mãi') }} - {{ $promotion->code }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="p-8">
                <form action="{{ route('owner.promotions.update', $promotion) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="promo-code" class="label-eyebrow block mb-2">Mã Khuyến Mãi *</label>
                        <input id="promo-code" type="text" name="code" value="{{ old('code', $promotion->code) }}" required class="input-base uppercase">
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="promo-description" class="label-eyebrow block mb-2">Mô tả</label>
                        <input id="promo-description" type="text" name="description" value="{{ old('description', $promotion->description) }}" class="input-base">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-discount-type" class="label-eyebrow block mb-2">Loại Giảm Giá *</label>
                            <select id="promo-discount-type" name="discount_type" class="input-base">
                                <option value="percent" @selected(old('discount_type', $promotion->discount_type) === 'percent')>Phần trăm (%)</option>
                                <option value="fixed" @selected(old('discount_type', $promotion->discount_type) === 'fixed')>Số tiền cố định (VNĐ)</option>
                            </select>
                            @error('discount_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="promo-discount-value" class="label-eyebrow block mb-2">Giá Trị Giảm *</label>
                            <input id="promo-discount-value" type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" required class="input-base">
                            @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-min-amount" class="label-eyebrow block mb-2">Đơn hàng tối thiểu (VNĐ)</label>
                            <input id="promo-min-amount" type="number" step="0.01" name="min_amount" value="{{ old('min_amount', $promotion->min_amount) }}" class="input-base">
                            @error('min_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="promo-max-uses" class="label-eyebrow block mb-2">Giới hạn số lần dùng</label>
                            <input id="promo-max-uses" type="number" name="max_uses" value="{{ old('max_uses', $promotion->max_uses) }}" class="input-base">
                            @error('max_uses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-starts" class="label-eyebrow block mb-2">Thời gian bắt đầu *</label>
                            <input id="promo-starts" type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d\TH:i')) }}" required class="input-base">
                            @error('starts_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="promo-expires" class="label-eyebrow block mb-2">Thời gian kết thúc *</label>
                            <input id="promo-expires" type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($promotion->expires_at)->format('Y-m-d\TH:i')) }}" required class="input-base">
                            @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-eyebrow block mb-2">Trạng Thái Kích Hoạt</label>
                        <div class="flex items-center mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                            <span class="ml-2 text-sm text-zinc-600 dark:text-zinc-400 font-medium">Đang cho phép sử dụng</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.venues.promotions.index', $promotion->venue_id) }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary">
                            Cập Nhật Mã
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
