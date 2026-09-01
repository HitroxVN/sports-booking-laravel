<x-owner-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
            {{ __('Quản Lý Đánh Giá Từ Khách Hàng') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Phản hồi và quản lý hiển thị các đánh giá của khách hàng</p>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="card-base p-8">
            <div class="space-y-6">
                @forelse($reviews as $review)
                    <div class="p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                            <div>
                                <div class="flex items-center flex-wrap gap-2">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 text-base">{{ $review->user->name ?? 'Khách ẩn danh' }}</span>
                                    <x-badge variant="info">Sân: {{ $review->venue->name }}</x-badge>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Đơn đặt: #{{ $review->booking->code ?? 'N/A' }} | Ngày: {{ $review->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-1 text-amber-400 dark:text-amber-500 font-bold text-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                                <span class="text-zinc-600 dark:text-zinc-400 text-sm ml-2">({{ $review->rating }}/5)</span>
                            </div>
                        </div>

                        <p class="text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 italic">
                            "{{ $review->comment }}"
                        </p>

                        <!-- Form phản hồi của chủ sân -->
                        <form action="{{ route('owner.reviews.update', $review) }}" method="POST" class="mt-4 space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="label-eyebrow block mb-1">Phản hồi của Chủ Sân</label>
                                <textarea name="owner_reply" rows="2" placeholder="Nhập câu trả lời hoặc cảm ơn khách hàng..." class="input-base text-sm">{{ old('owner_reply', $review->owner_reply) }}</textarea>
                            </div>

                            <div class="flex justify-between items-center flex-wrap gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_visible" value="1" @checked($review->is_visible) class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 bg-white dark:bg-zinc-800">
                                    <span class="ml-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300">Hiển thị công khai</span>
                                </label>

                                <button type="submit" class="btn-primary text-xs">
                                        Gửi Phản Hồi
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <x-empty-state icon="⭐" title="Chưa có đánh giá nào từ khách hàng" description="Các đánh giá sau khi khách hoàn tất trận đấu sẽ xuất hiện tại đây." />
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</x-owner-layout>
