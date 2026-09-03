function initBookingGrid() {
    if (typeof Alpine === 'undefined') return;

    Alpine.data('bookingGrid', (config = {}) => ({
        selectedDate: config.initialDate || '',
        startSlotIdx: null,
        endSlotIdx: null,
        courtSlots: Array.isArray(config.courtSlots) ? config.courtSlots : [],
        existingBookings: Array.isArray(config.existingBookings) ? config.existingBookings : [],
        closures: Array.isArray(config.closures) ? config.closures : [],

        get availableSlots() {
            let slots = [];
            for (let hour = 5; hour < 22; hour++) {
                let startStr = (hour < 10 ? '0' : '') + hour + ':00';
                let endStr = (hour + 1 < 10 ? '0' : '') + (hour + 1) + ':00';

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

                // Bắt lỗi null/undefined cho courtSlots
                let matchingSlot = this.courtSlots.find(s => {
                    if (!s || !s.start_time || !s.end_time) return false;
                    let sStart = String(s.start_time).substring(0, 5);
                    let sEnd = String(s.end_time).substring(0, 5);
                    return startStr >= sStart && endStr <= sEnd;
                });

                let price = 100000;
                if (matchingSlot) {
                    price = (matchingSlot.is_peak && matchingSlot.peak_price)
                        ? parseFloat(matchingSlot.peak_price)
                        : parseFloat(matchingSlot.price || 100000);
                }

                slots.push({
                    start: startStr,
                    end: endStr,
                    price: price,
                    isBooked: isBooked || isClosed,
                    isClosed: isClosed
                });
            }
            return slots;
        },

        selectSlot(idx) {
            if (!this.availableSlots[idx] || this.availableSlots[idx].isBooked) return;

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
                    let hasBooked = false;
                    for (let i = this.startSlotIdx; i <= idx; i++) {
                        if (this.availableSlots[i].isBooked) {
                            hasBooked = true;
                            break;
                        }
                    }

                    if (hasBooked) {
                        alert('Không thể chọn khoảng giờ có chứa khung đã được đặt!');
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
                    total += this.availableSlots[i].price;
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