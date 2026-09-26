@auth
@php
    // Chuông chỉ cần 8 thông báo gần nhất; trang /notifications có danh sách đầy đủ
    $bellUser = auth()->user();
    $bellData = [
        'unread'  => $bellUser->unreadNotifications()->count(),
        'items'   => $bellUser->notifications()->take(8)->get()->map(fn ($n) => [
            'id'      => $n->id,
            'title'   => $n->data['title'] ?? 'Thông báo',
            'message' => $n->data['message'] ?? '',
            'url'     => $n->data['url'] ?? '',
            'level'   => $n->data['level'] ?? 'info',
            'read'    => $n->read_at !== null,
            'time'    => $n->created_at?->locale('vi')->diffForHumans(),
        ])->all(),
        'csrf'    => csrf_token(),
        'readUrl' => route('notifications.read', ['notification' => '__ID__']),
        'allUrl'  => route('notifications.read-all'),
        'allPage' => route('notifications.index'),
    ];
@endphp

<div x-data="notificationBell(@js($bellData))" @click.outside="open = false" class="relative">
    <button @click="open = !open" aria-label="Thông báo"
            class="relative flex items-center justify-center w-9 h-9 rounded-xl text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" style="display: none;"
              class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full"></span>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         style="display: none;"
         class="absolute right-0 z-50 mt-2 w-80 sm:w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Thông báo</h4>
            <button type="button" @click="markAll()" x-show="unread > 0" style="display: none;"
                    class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline">
                Đánh dấu tất cả đã đọc
            </button>
        </div>

        {{-- Danh sách --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
            <template x-for="item in items" :key="item.id">
                <a :href="item.url || '{{ route('notifications.index') }}'"
                   @click="markRead(item.id)"
                   class="flex gap-3 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors"
                   :class="item.read ? '' : 'bg-primary-50/60 dark:bg-primary-900/20'">

                    {{-- Chấm màu theo mức độ --}}
                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0"
                          :class="{
                              'bg-emerald-500': item.level === 'success',
                              'bg-amber-500':   item.level === 'warning',
                              'bg-red-500':     item.level === 'danger',
                              'bg-sky-500':     item.level === 'info',
                          }"></span>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" x-text="item.title"></p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2" x-text="item.message"></p>
                        <p class="text-[11px] text-zinc-400 mt-1" x-text="item.time"></p>
                    </div>
                </a>
            </template>

            {{-- Rỗng --}}
            <div x-show="items.length === 0" class="px-4 py-10 text-center">
                <svg class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Chưa có thông báo nào</p>
            </div>
        </div>

        {{-- Footer --}}
        <a :href="allPage"
           class="block px-4 py-3 text-center text-sm font-medium text-primary-600 dark:text-primary-400 bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
            Xem tất cả thông báo
        </a>
    </div>
</div>

<script>
    function notificationBell(config) {
        return {
            open: false,
            items: config.items || [],
            unread: config.unread || 0,
            csrf: config.csrf,
            readUrl: config.readUrl,
            allUrl: config.allUrl,
            allPage: config.allPage,

            markRead(id) {
                const item = this.items.find(i => i.id === id);
                if (item && !item.read) {
                    item.read = true;
                    this.unread = Math.max(0, this.unread - 1);
                }
                this.post(this.readUrl.replace('__ID__', id));
            },

            markAll() {
                this.items.forEach(i => i.read = true);
                this.unread = 0;
                this.post(this.allUrl);
            },

            // Không chờ kết quả: đánh dấu đọc là việc phụ, hỏng cũng không sao
            post(url) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json'
                    }
                }).catch(() => {});
            },
        }
    }
</script>
@endauth
