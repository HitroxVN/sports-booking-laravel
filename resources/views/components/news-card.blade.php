@props(['article'])

<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-lc transition-all duration-300 hover:-translate-y-1 hover:border-primary-300 hover:shadow-lc-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-primary-700">
    <div class="aspect-[16/9] overflow-hidden bg-primary-50 dark:bg-zinc-800">
        <img
            src="{{ $article->thumbnail ?: asset('images/defaults/news.svg') }}"
            alt="{{ $article->title }}"
            loading="lazy"
            referrerpolicy="no-referrer"
            onerror="this.onerror=null;this.src='{{ asset('images/defaults/news.svg') }}';"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
        >
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="mb-3 flex items-center justify-between gap-3">
            <span @class([
                'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300' => $article->source === 'VnExpress',
                'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300' => $article->source === 'Thanh Niên',
            ])>
                {{ $article->source }}
            </span>
            <time datetime="{{ $article->published_at->toIso8601String() }}" class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ $article->published_at->format('H:i d/m/Y') }}
            </time>
        </div>

        <h2 class="line-clamp-2 text-lg font-bold leading-snug text-zinc-900 transition-colors group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-300">
            {{ $article->title }}
        </h2>

        @if($article->summary)
            <p class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                {{ $article->summary }}
            </p>
        @endif

        <a
            href="{{ $article->source_url }}"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-1.5 self-start text-sm font-semibold text-primary-600 transition-colors hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
        >
            Xem bài viết gốc <span aria-hidden="true">↗</span>
            <span class="sr-only">(mở trong tab mới)</span>
        </a>
    </div>
</article>
