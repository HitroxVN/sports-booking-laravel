/**
 * TOAST (SNACKBAR) — Alpine store dùng chung cho mọi trang khách.
 *
 * - Live region: role="status" (polite) cho success/info · role="alert" (assertive) cho error/warning
 *   → markup nằm trong resources/views/components/lc-toast.blade.php
 * - success/info (thông báo phụ)   → tự ẩn sau vài giây
 * - error/warning (thiết yếu)      → giữ tới khi người dùng đóng tay
 * - Timer TẠM DỪNG khi hover hoặc bàn phím đang focus trong toast (pause/resume)
 * - Class màu sắc KHÔNG đặt ở đây — Tailwind không scan file JS;
 *   map class được truyền từ Blade vào lcToastRegion qua @js($ui).
 *
 * Gọi từ JS bất kỳ: toast('Đã đặt sân!', 'success', { title: 'Hoàn tất' })
 */

import Alpine from 'alpinejs';

// null = thông báo THIẾT YẾU → không tự ẩn
const DURATIONS = {
    success: 5000,
    info: 6500,
    error: null,
    warning: null,
};
const ESSENTIAL_TYPES = ['error', 'warning'];
const LEAVE_MS = 260;   // khớp thời lượng animation trượt ra (.lc-toast--leaving)
const MAX_VISIBLE = 4;  // tràn màn hình → đóng bớt toast cũ nhất

Alpine.store('toast', {
    items: [],
    _seq: 0,

    /** Toast phụ (tự ẩn) — vùng role="status". */
    get nonEssentials() {
        return this.items.filter((i) => !ESSENTIAL_TYPES.includes(i.type));
    },

    /** Toast thiết yếu (giữ lại) — vùng role="alert". */
    get essentials() {
        return this.items.filter((i) => ESSENTIAL_TYPES.includes(i.type));
    },

    /** Thêm toast mới. Trả về id để có thể đóng chủ động. */
    push(message, type = 'info', opts = {}) {
        const duration = opts.duration ?? (type in DURATIONS ? DURATIONS[type] : DURATIONS.info);
        const item = {
            id: ++this._seq,
            type,
            title: opts.title ?? null,
            message: String(message ?? ''),
            duration,
            leaving: false,
        };
        this.items.push(item);

        const live = this.items.filter((i) => !i.leaving);
        if (live.length > MAX_VISIBLE) this.dismiss(live[0].id, true);

        if (duration) {
            item._deadline = Date.now() + duration;
            item._remaining = duration;
            item._tid = setTimeout(() => this.dismiss(item.id), duration);
        }
        return item.id;
    },

    /** Đánh dấu ẩn (chạy animation) rồi gỡ khỏi danh sách sau LEAVE_MS. */
    dismiss(id, immediate = false) {
        const idx = this.items.findIndex((i) => i.id === id);
        if (idx === -1) return;
        const item = this.items[idx];
        if (item.leaving && !immediate) return;

        item.leaving = true;
        if (item._tid) { clearTimeout(item._tid); item._tid = null; }

        const remove = () => {
            const j = this.items.findIndex((i) => i.id === id);
            if (j !== -1) this.items.splice(j, 1);
        };
        if (immediate) remove();
        else setTimeout(remove, LEAVE_MS);
    },

    /** Tạm dừng đếm ngược khi chuột trỏ vào / bàn phím đang trong toast. */
    pause(item) {
        if (!item.duration || item._tid == null) return;
        clearTimeout(item._tid);
        item._tid = null;
        item._remaining = Math.max(0, item._deadline - Date.now());
    },

    /** Chạy tiếp với đúng thời gian còn lại. */
    resume(item) {
        if (!item.duration || item._tid != null || item.leaving) return;
        const remaining = item._remaining ?? item.duration;
        item._deadline = Date.now() + remaining;
        item._tid = setTimeout(() => this.dismiss(item.id), remaining);
    },
});

/**
 * Component vùng chứa — nhận flash messages từ server để đẩy vào store.
 * $ui: map type → class màu (định nghĩa trong Blade cho Tailwind scan).
 */
Alpine.data('lcToastRegion', (flashes = [], ui = {}) => ({
    ui,

    init() {
        flashes.forEach((f) => this.$store.toast.push(f.message, f.type));
    },
}));

// Helper toàn cục: toast('Lưu thành công', 'success', { title: 'Đã lưu' })
window.toast = (message, type = 'info', opts = {}) => Alpine.store('toast').push(message, type, opts);
