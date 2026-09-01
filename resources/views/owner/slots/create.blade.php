<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.courts.slots.index', $court) }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Thiết lập Khung Giờ - {{ $court->name }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="p-8">
                <form action="{{ route('owner.courts.slots.store', $court) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="slot-day" class="label-eyebrow block mb-2">Ngày áp dụng <span class="text-red-500">*</span></label>
                        <select id="slot-day" name="day_of_week" required class="input-base">
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
                            <label for="slot-start" class="label-eyebrow block mb-2">Giờ bắt đầu <span class="text-red-500">*</span></label>
                            <input id="slot-start" type="time" name="start_time" required class="input-base">
                            @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="slot-end" class="label-eyebrow block mb-2">Giờ kết thúc <span class="text-red-500">*</span></label>
                            <input id="slot-end" type="time" name="end_time" required class="input-base">
                            @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 items-start">
                        <div>
                            <label for="slot-price" class="label-eyebrow block mb-2">Giá thường (VNĐ) <span class="text-red-500">*</span></label>
                            <input id="slot-price" type="number" name="price" required min="0" placeholder="VD: 300000" class="input-base">
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{ isPeak: false }">
                            <div class="mb-2 mt-1">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="is_peak" value="0">
                                    <input type="checkbox" name="is_peak" value="1" x-model="isPeak" class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                                    <span class="ml-2 font-semibold text-zinc-700 dark:text-zinc-300">Là Giờ Vàng?</span>
                                </label>
                            </div>

                            <div x-show="isPeak" x-transition>
                                <label for="slot-peak-price" class="label-eyebrow block mb-2">Giá giờ vàng (VNĐ) <span class="text-red-500">*</span></label>
                                <input id="slot-peak-price" type="number" name="peak_price" min="0" placeholder="VD: 500000" class="input-base">
                                @error('peak_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.courts.slots.index', $court) }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary">
                            Lưu Khung Giờ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
