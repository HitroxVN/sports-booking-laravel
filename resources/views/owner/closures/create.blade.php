<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Thêm Lịch Khóa Sân — {{ $court->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('owner.courts.closures.index', $court) }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                <form action="{{ route('owner.courts.closures.store', $court) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày Khóa Sân <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ fullDay: true }">
                        <label class="inline-flex items-center cursor-pointer mb-4 mt-2">
                            <input type="checkbox" x-model="fullDay" class="rounded border-gray-300 text-pink-500 focus:ring-pink-500 w-5 h-5">
                            <span class="ml-2 font-bold text-gray-700">Khóa cả ngày</span>
                        </label>

                        <div x-show="!fullDay" class="grid grid-cols-2 gap-6" x-transition>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Giờ bắt đầu</label>
                                <input type="time" name="start_time" x-bind:required="!fullDay" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Giờ kết thúc</label>
                                <input type="time" name="end_time" x-bind:required="!fullDay" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                                @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lý do khóa sân <span class="text-red-500">*</span></label>
                        <input type="text" name="reason" value="{{ old('reason') }}" required placeholder="VD: Bảo trì lưới, Cho thuê quay phim, Mưa bão..." class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                        @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                            Xác Nhận Khóa Lịch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>