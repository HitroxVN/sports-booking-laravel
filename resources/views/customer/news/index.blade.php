@extends('layouts.customer')

@section('content')
<section class="bg-tint-sky py-12 sm:py-16 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-9 max-w-2xl">
            <span class="text-sm font-bold uppercase tracking-widest text-primary-600 dark:text-primary-400">Arena Sports News</span>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl dark:text-white">Tin tức thể thao</h1>
            <p class="mt-3 text-base leading-7 text-zinc-600 dark:text-zinc-400">
                Cập nhật những diễn biến thể thao mới nhất từ VnExpress và Thanh Niên.
            </p>
        </div>

        @if($articles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <x-news-card :article="$article" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center shadow-lc dark:border-zinc-700 dark:bg-zinc-900">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9l5 5v9a2 2 0 0 1-2 2ZM14 4v5h5M7 13h10M7 16h7" />
                </svg>
                <h2 class="mt-4 text-lg font-bold text-zinc-900 dark:text-white">Chưa có tin thể thao</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Tin mới sẽ xuất hiện sau lần đồng bộ RSS tiếp theo.</p>
            </div>
        @endif
    </div>
</section>
@endsection
