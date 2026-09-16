{{-- ================================================================
     TOAST / SNACKBAR — vùng thông báo góc trên phải, không che tương tác trang
     - 2 live region: role="status" (thông báo phụ, tự ẩn)
                      role="alert"  (lỗi/cảnh báo thiết yếu, giữ tới khi đóng)
     - Timer TẠM DỪNG khi hover hoặc bàn phím focus trong toast
     - Logic: resources/js/Toast.js · Animation: resources/css/app.css
     - Dùng: <x-lc-toast /> trong layout; từ JS: toast('msg', 'success')
     - Map class màu đặt ở Blade (Tailwind không scan file JS)
================================================================= --}}
@php
    $toastUi = [
        'success' => ['ring' => 'ring-accent-moss/40', 'icon' => 'bg-accent-moss',  'bar' => 'bg-accent-moss'],
        'error'   => ['ring' => 'ring-red-500/50',     'icon' => 'bg-red-500',      'bar' => 'bg-red-500'],
        'warning' => ['ring' => 'ring-amber-500/50',   'icon' => 'bg-amber-500',    'bar' => 'bg-amber-500'],
        'info'    => ['ring' => 'ring-primary-600/40', 'icon' => 'bg-primary-600',  'bar' => 'bg-primary-600'],
    ];

    // Flash messages từ server → đẩy vào store khi Alpine khởi động
    $toasts = [];
    if (session('success')) $toasts[] = ['type' => 'success', 'message' => session('success')];
    if (session('status'))  $toasts[] = ['type' => 'success', 'message' => session('status')];
    if (session('error'))   $toasts[] = ['type' => 'error',   'message' => session('error')];
    if (session('warning')) $toasts[] = ['type' => 'warning', 'message' => session('warning')];
    if (session('info'))    $toasts[] = ['type' => 'info',    'message' => session('info')];
@endphp

<div x-cloak
     x-data="lcToastRegion(@js($toasts), @js($toastUi))"
     class="fixed top-20 right-4 z-[100] flex flex-col items-end gap-3 w-[calc(100vw-2rem)] max-w-sm pointer-events-none"
     aria-label="Thông báo">

    {{-- ── Vùng 1 · role="status" — thông báo PHỤ (success/info), tự ẩn sau vài giây ── --}}
    <div role="status" aria-live="polite" class="contents">
        <template x-for="item in $store.toast.nonEssentials" :key="item.id">
            <div @mouseenter="$store.toast.pause(item)" @mouseleave="$store.toast.resume(item)"
                 @focusin="$store.toast.pause(item)" @focusout="if (!$el.contains($event.relatedTarget)) $store.toast.resume(item)"
                 :class="['lc-toast pointer-events-auto relative flex items-start gap-2.5 w-full overflow-hidden rounded-2xl bg-white/95 dark:bg-zinc-900/95 border border-zinc-200 dark:border-zinc-700 ring-1 shadow-lc-lg backdrop-blur-sm', ui[item.type]?.ring || 'ring-transparent', item.leaving ? 'lc-toast--leaving' : '']">
                {{-- sọc màu nhận diện loại --}}
                <span class="absolute left-0 top-0 h-full w-1" :class="ui[item.type]?.bar" aria-hidden="true"></span>

                {{-- icon tròn --}}
                <span class="shrink-0 mt-3 ml-2.5 flex items-center justify-center w-7 h-7 rounded-full text-white" :class="ui[item.type]?.icon" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              :d="item.type === 'success' ? 'M5 13l4 4L19 7' : 'M12 8v5m0 3h.01'"/>
                    </svg>
                </span>

                {{-- nội dung --}}
                <div class="flex-1 min-w-0 py-3">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-50" x-text="item.title || (item.type === 'success' ? 'Thành công' : 'Thông tin')"></p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300 mt-0.5 break-words leading-snug" x-text="item.message"></p>
                </div>

                {{-- nút đóng --}}
                <button type="button" @click="$store.toast.dismiss(item.id)"
                        class="shrink-0 self-stretch p-2.5 mr-1 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                        aria-label="Đóng thông báo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- thanh đếm ngược — hết = tự ẩn; pause đồng bộ với hover/focus --}}
                <template x-if="item.duration">
                    <span class="lc-toast__timer absolute bottom-0 left-0 h-0.5"
                          :class="ui[item.type]?.bar"
                          :style="`animation-duration:${item.duration}ms`"
                          aria-hidden="true"></span>
                </template>
            </div>
        </template>
    </div>

    {{-- ── Vùng 2 · role="alert" — THIẾT YẾU (error/warning), giữ tới khi người dùng đóng ── --}}
    <div role="alert" aria-live="assertive" class="contents">
        <template x-for="item in $store.toast.essentials" :key="item.id">
            <div @mouseenter="$store.toast.pause(item)" @mouseleave="$store.toast.resume(item)"
                 @focusin="$store.toast.pause(item)" @focusout="if (!$el.contains($event.relatedTarget)) $store.toast.resume(item)"
                 :class="['lc-toast pointer-events-auto relative flex items-start gap-2.5 w-full overflow-hidden rounded-2xl bg-white/95 dark:bg-zinc-900/95 border border-zinc-200 dark:border-zinc-700 ring-1 shadow-lc-lg backdrop-blur-sm', ui[item.type]?.ring || 'ring-transparent', item.leaving ? 'lc-toast--leaving' : '']">
                <span class="absolute left-0 top-0 h-full w-1" :class="ui[item.type]?.bar" aria-hidden="true"></span>
                <span class="shrink-0 mt-3 ml-2.5 flex items-center justify-center w-7 h-7 rounded-full text-white" :class="ui[item.type]?.icon" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    </svg>
                </span>
                <div class="flex-1 min-w-0 py-3">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-50" x-text="item.title || (item.type === 'error' ? 'Có lỗi xảy ra' : 'Cảnh báo')"></p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300 mt-0.5 break-words leading-snug" x-text="item.message"></p>
                </div>
                <button type="button" @click="$store.toast.dismiss(item.id)"
                        class="shrink-0 self-stretch p-2.5 mr-1 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                        aria-label="Đóng thông báo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>
