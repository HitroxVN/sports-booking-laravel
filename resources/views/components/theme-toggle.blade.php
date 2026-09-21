{{-- Nút chuyển giao diện sáng/tối — dùng chung mọi layout.
    Đọc trạng thái từ <html class="dark"> (script FOUC trong <head> đã set trước). --}}
<button type="button"
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            try { localStorage.setItem('color-mode', this.dark ? 'dark' : 'light'); } catch (e) {}
        }
    }"
    @click="toggle()"
    :aria-label="dark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối'"
    class="w-9 h-9 flex items-center justify-center rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500">
    <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-cloak>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.352 8.964a8 8 0 01-9.388-9.388 8 8 0 109.388 9.388z" />
    </svg>
    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-cloak>
        <circle cx="12" cy="12" r="4" stroke-width="2" />
        <path stroke-linecap="round" stroke-width="2" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
    </svg>
</button>
