function initBookingGrid() {
    if (typeof Alpine === 'undefined') return;

    Alpine.data('bookingGrid', (config = {}) => ({
        selectedDate: config.initialDate || '',
        startSlotIdx: null,
        endSlotIdx: null,
        // Ô giờ đã được server cắt sẵn theo cấu hình khung giờ của chủ sân (theo từng ngày)
        slotCells: (config.slotCells && typeof config.slotCells === 'object') ? config.slotCells : {},
        existingBookings: Array.isArray(config.existingBookings) ? config.existingBookings : [],
        closures: Array.isArray(config.closures) ? config.closures : [],
        // Giờ hoạt động của khu sân theo ngày trong tuần (0 = CN ... 6 = Thứ 7)
        operatingHours: Array.isArray(config.operatingHours) ? config.operatingHours : [],
        // Khu sân có cài giờ hoạt động không — chưa cài thì không chặn theo giờ hoạt động
        venueHasOperatingHours: !!config.venueHasOperatingHours,

        // Ô giờ của ngày đang chọn — chủ sân chưa cài khung giờ thì ngày đó không có ô nào
        get availableSlots() {
            return this.slotCells[this.selectedDate] || [];
        },

        get hasNoSlots() {
            return this.availableSlots.length === 0;
        },

        // Giờ hoạt động của ngày đang chọn (null nếu khu sân không cài ngày đó)
        get dayOperatingHour() {
            if (!this.selectedDate) return null;
            let dow = new Date(this.selectedDate + 'T00:00:00').getDay(); // 0 = CN ... 6 = Thứ 7
            return this.operatingHours.find(h => h && Number(h.day_of_week) === dow) || null;
        },

        isSlotBooked(cell) {
            // Ô ngoài giờ mở bán (khoảng trống giữa 2 khung chủ sân cài)
            if (!cell.is_open) return true;

            let startStr = cell.start;
            let endStr = cell.end;

            // Bắt lỗi null/undefined cho existingBookings
            let isBooked = this.existingBookings.some(b => {
                if (!b || b.booking_date !== this.selectedDate) return false;
                let bStart = b.start_time ? String(b.start_time).substring(0, 5) : '';
                let bEnd = b.end_time ? String(b.end_time).substring(0, 5) : '';
                return (startStr < bEnd && endStr > bStart);
            });

            // Bắt lỗi null/undefined cho closures
            let isClosed = this.closures.some(c => {
                if (!c || c.date !== this.selectedDate) return false;
                if (!c.start_time) return true;
                let cStart = String(c.start_time).substring(0, 5);
                let cEnd = String(c.end_time).substring(0, 5);
                return (startStr < cEnd && endStr > cStart);
            });

            return isBooked || isClosed;
        },

        // Lý do ô không đặt được: 'closed' (ngoài khung), 'booked', 'closure'
        slotBlockedReason(cell) {
            if (!cell.is_open) return 'closed';

            let isClosed = this.closures.some(c => {
                if (!c || c.date !== this.selectedDate) return false;
                if (!c.start_time) return true;
                let cStart = String(c.start_time).substring(0, 5);
                let cEnd = String(c.end_time).substring(0, 5);
                return (cell.start < cEnd && cell.end > cStart);
            });
            if (isClosed) return 'closure';

            let isBooked = this.existingBookings.some(b => {
                if (!b || b.booking_date !== this.selectedDate) return false;
                let bStart = b.start_time ? String(b.start_time).substring(0, 5) : '';
                let bEnd = b.end_time ? String(b.end_time).substring(0, 5) : '';
                return (cell.start < bEnd && cell.end > bStart);
            });
            if (isBooked) return 'booked';

            return null;
        },

        selectSlot(idx) {
            let cell = this.availableSlots[idx];
            if (!cell || this.isSlotBooked(cell)) return;

            if (this.startSlotIdx === null || (this.startSlotIdx !== null && this.endSlotIdx !== null)) {
                this.startSlotIdx = idx;
                this.endSlotIdx = null;
            } else {
                if (idx < this.startSlotIdx) {
                    this.startSlotIdx = idx;
                    this.endSlotIdx = null;
                } else if (idx === this.startSlotIdx) {
                    this.endSlotIdx = idx;
                } else {
                    let hasBlocked = false;
                    for (let i = this.startSlotIdx; i <= idx; i++) {
                        if (this.isSlotBooked(this.availableSlots[i])) {
                            hasBlocked = true;
                            break;
                        }
                    }

                    if (hasBlocked) {
                        alert('Không thể chọn khoảng giờ có chứa khung đã được đặt hoặc đã khóa!');
                        this.startSlotIdx = idx;
                        this.endSlotIdx = null;
                    } else {
                        this.endSlotIdx = idx;
                    }
                }
            }
        },

        isSlotSelected(idx) {
            if (this.startSlotIdx === null) return false;
            let start = this.startSlotIdx;
            let end = this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx;
            return idx >= Math.min(start, end) && idx <= Math.max(start, end);
        },

        resetSelection() {
            this.startSlotIdx = null;
            this.endSlotIdx = null;
        },

        selectDate(date) {
            this.selectedDate = date;
            this.resetSelection();
        },

        get selectedStart() {
            if (this.startSlotIdx === null || !this.availableSlots[this.startSlotIdx]) return '';
            let start = Math.min(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
            return this.availableSlots[start].start;
        },

        get selectedEnd() {
            if (this.startSlotIdx === null || !this.availableSlots[this.startSlotIdx]) return '';
            let end = Math.max(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
            return this.availableSlots[end].end;
        },

        get calculatedPrice() {
            if (this.startSlotIdx === null) return 0;
            let start = Math.min(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
            let end = Math.max(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);

            let total = 0;
            for (let i = start; i <= end; i++) {
                if (this.availableSlots[i]) {
                    total += parseFloat(this.availableSlots[i].price) || 0;
                }
            }
            return total;
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount || 0);
        }
    }));
}

// Đảm bảo đăng ký component dù Alpine được load theo cách nào
if (window.Alpine) {
    initBookingGrid();
} else {
    document.addEventListener('alpine:init', initBookingGrid);
}
