<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.courts.closures.index', $court) }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition mb-2">
                &larr; Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Thêm Lịch Khóa Sân - {{ $court->name }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base">
            <div class="p-8">
                <form action="{{ route('owner.courts.closures.store', $court) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="closure-date" class="label-eyebrow block mb-2">Ngày Khóa Sân <span class="text-red-500">*</span></label>
                        <input id="closure-date" type="date" name="date" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}" class="input-base">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ fullDay: true }">
                        <label class="inline-flex items-center cursor-pointer mb-4 mt-2">
                            <input type="checkbox" x-model="fullDay" class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                            <span class="ml-2 font-semibold text-zinc-700 dark:text-zinc-300">Khóa cả ngày</span>
                        </label>

                        <div x-show="!fullDay" class="grid grid-cols-2 gap-6" x-transition>
                            <div>
                                <label for="closure-start" class="label-eyebrow block mb-2">Giờ bắt đầu</label>
                                <input id="closure-start" type="time" name="start_time" x-bind:required="!fullDay" class="input-base">
                                @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="closure-end" class="label-eyebrow block mb-2">Giờ kết thúc</label>
                                <input id="closure-end" type="time" name="end_time" x-bind:required="!fullDay" class="input-base">
                                @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="closure-reason" class="label-eyebrow block mb-2">Lý do khóa sân <span class="text-red-500">*</span></label>
                        <input id="closure-reason" type="text" name="reason" value="{{ old('reason') }}" required placeholder="VD: Bảo trì lưới, Cho thuê quay phim, Mưa bão..." class="input-base">
                        @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('owner.courts.closures.index', $court) }}" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-danger">
                            Xác Nhận Khóa Lịch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
