<x-owner-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Tạo Mã Khuyến Mãi - {{ $venue->name }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="p-8">
                <form action="{{ route('owner.venues.promotions.store', $venue) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-code" class="label-eyebrow block mb-2">Mã Code (Chữ in hoa) <span class="text-red-500">*</span></label>
                            <input id="promo-code" type="text" name="code" value="{{ old('code') }}" required placeholder="VD: SUMMER28" class="input-base uppercase">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label-eyebrow block mb-2">Trạng Thái Kích Hoạt</label>
                            <label class="inline-flex items-center mt-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                <span class="ml-2 font-semibold text-zinc-700 dark:text-zinc-300">Đang bật</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="promo-description" class="label-eyebrow block mb-2">Mô tả chương trình</label>
                        <input id="promo-description" type="text" name="description" value="{{ old('description') }}" placeholder="VD: Khuyến mãi cho anh em FC Mạch Tràng nhân dịp giải đấu..." class="input-base">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-discount-type" class="label-eyebrow block mb-2">Loại Giảm Giá <span class="text-red-500">*</span></label>
                            <select id="promo-discount-type" name="discount_type" class="input-base">
                                <option value="percent">Giảm theo phần trăm (%)</option>
                                <option value="fixed">Giảm số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div>
                            <label for="promo-discount-value" class="label-eyebrow block mb-2">Mức Giảm <span class="text-red-500">*</span></label>
                            <input id="promo-discount-value" type="number" name="discount_value" value="{{ old('discount_value') }}" required min="0" placeholder="VD: 10 hoặc 50000" class="input-base">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-min-amount" class="label-eyebrow block mb-2">Đơn Tối Thiểu Được Áp Dụng (VNĐ)</label>
                            <input id="promo-min-amount" type="number" name="min_amount" value="{{ old('min_amount') }}" min="0" placeholder="VD: 200000 (Để trống nếu không giới hạn)" class="input-base">
                        </div>
                        <div>
                            <label for="promo-max-uses" class="label-eyebrow block mb-2">Giới hạn số lần dùng</label>
                            <input id="promo-max-uses" type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="VD: 100 (Để trống nếu không giới hạn)" class="input-base">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="promo-starts" class="label-eyebrow block mb-2">Ngày bắt đầu <span class="text-red-500">*</span></label>
                            <input id="promo-starts" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="input-base">
                        </div>
                        <div>
                            <label for="promo-expires" class="label-eyebrow block mb-2">Ngày kết thúc <span class="text-red-500">*</span></label>
                            <input id="promo-expires" type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" required class="input-base">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary">
                            Tạo Khuyến Mãi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-owner-layout>
