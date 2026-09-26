@php
    // Dùng chung cho trang của khách và panel owner/admin
    $markUrlTemplate = route('notifications.read', ['notification' => '__ID__']);
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100">Thông báo</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $unreadCount > 0 ? "Bạn có {$unreadCount} thông báo chưa đọc." : 'Tất cả thông báo đã được đọc.' }}
            </p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold rounded-xl text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/30 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                    Đánh dấu tất cả đã đọc
                </button>
            </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $item)
            @php
                $payload = $item->data;
                $unread  = $item->read_at === null;
                $dot     = match ($payload['level'] ?? 'info') {
                    'success' => 'bg-emerald-500',
                    'warning' => 'bg-amber-500',
                    'danger'  => 'bg-red-500',
                    default   => 'bg-sky-500',
                };
                $target = ($payload['url'] ?? '') !== '' ? $payload['url'] : route('notifications.index');
            @endphp

            <a href="{{ $target }}"
               @if($unread) onclick="arenaMarkNotificationRead('{{ $item->id }}')" @endif
               class="block rounded-2xl border px-4 py-3.5 transition-colors {{ $unread
                    ? 'bg-primary-50/60 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800 hover:bg-primary-50 dark:hover:bg-primary-900/30'
                    : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                <div class="flex gap-3">
                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $dot }}"></span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $payload['title'] ?? 'Thông báo' }}
                            </p>
                            <span class="text-[11px] text-zinc-400 whitespace-nowrap shrink-0">
                                {{ $item->created_at?->locale('vi')->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $payload['message'] ?? '' }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="py-16 text-center rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <svg class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Chưa có thông báo nào.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</div>

<script>
    // Đánh dấu đã đọc nhưng KHÔNG chờ kết quả — click phải điều hướng ngay.
    // keepalive để request sống sót qua lần chuyển trang.
    function arenaMarkNotificationRead(id) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) return;

        fetch('{{ $markUrlTemplate }}'.replace('__ID__', id), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            keepalive: true,
        }).catch(() => {});
    }
</script>
