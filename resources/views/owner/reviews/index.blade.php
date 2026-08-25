<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Quản Lý Đánh Giá Từ Khách Hàng') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-white border-l-4 border-pink-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden p-8">
                <div class="space-y-8">
                    @forelse($reviews as $review)
                        <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-4">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-bold text-gray-800 text-lg">{{ $review->user->name ?? 'Khách ẩn danh' }}</span>
                                        <span class="text-xs bg-pink-100 text-pink-700 px-3 py-1 rounded-full font-semibold">Sân: {{ $review->venue->name }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Đơn đặt: #{{ $review->booking->code ?? 'N/A' }} | Ngày: {{ $review->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex items-center space-x-1 text-yellow-400 font-bold text-lg">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                    <span class="text-gray-700 text-sm ml-2">({{ $review->rating }}/5)</span>
                                </div>
                            </div>

                            <p class="text-gray-700 bg-white p-4 rounded-xl border border-gray-100 italic">
                                "{{ $review->comment }}"
                            </p>

                            <!-- Form phản hồi của chủ sân -->
                            <form action="{{ route('owner.reviews.update', $review) }}" method="POST" class="mt-4 space-y-3 pt-4 border-t border-gray-200">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Phản hồi của Chủ Sân</label>
                                    <textarea name="owner_reply" rows="2" placeholder="Nhập câu trả lời hoặc cảm ơn khách hàng..." class="w-full text-sm border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">{{ old('owner_reply', $review->owner_reply) }}</textarea>
                                </div>

                                <div class="flex justify-between items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_visible" value="1" @checked($review->is_visible) class="rounded border-gray-300 text-pink-500 focus:ring-pink-500">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Hiển thị công khai</span>
                                    </label>

                                    <button type="submit" class="px-5 py-2 bg-pink-500 hover:bg-pink-600 text-white font-bold text-xs rounded-xl shadow transition">
                                            Gửi Phản Hồi
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8">Chưa có đánh giá nào từ khách hàng.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>