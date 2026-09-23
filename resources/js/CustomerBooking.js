function initBookingGrid() {
    if (typeof Alpine === 'undefined') return;

    Alpine.data('bookingGrid', (config = {}) => ({
        selectedDate: config.initialDate || '',
        // Popup lịch tháng: mở/đóng + tháng đang xem (0-11) + năm
        showCalendar: false,
        calMonth: null,
        calYear: null,
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
        // Ngày muộn nhất đặt được (hết tháng sau) — chặn điều hướng lịch
        maxDate: config.maxDate || '',
        // Mã giảm giá đang chạy của khu sân (key = mã in hoa) — để ước lượng giá sau giảm
        promotions: (config.promotions && typeof config.promotions === 'object') ? config.promotions : {},
        // Mã khách nhập + kết quả kiểm tra (null = chưa kiểm tra)
        promoCode: '',
        promoMessage: '',
        promoValid: null,

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

        // Ô giờ hôm nay đã qua — không đặt được nữa (khớp chặn phía server: start_time < bây giờ)
        isSlotPast(cell) {
            const now = new Date();
            const today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            if (this.selectedDate !== today) return false;
            return cell.start <= now.toTimeString().slice(0, 5);
        },

        isSlotBooked(cell) {
            // Ô ngoài giờ mở bán (khoảng trống giữa 2 khung chủ sân cài)
            if (!cell.is_open) return true;

            // Ô đã qua của hôm nay
            if (this.isSlotPast(cell)) return true;

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

        // Lý do ô không đặt được: 'closed' (ngoài khung), 'booked', 'closure', 'past' (đã qua hôm nay)
        slotBlockedReason(cell) {
            if (!cell.is_open) return 'closed';
            if (this.isSlotPast(cell)) return 'past';

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
            this.showCalendar = false; // Chọn xong tự đóng popup
        },

        // Nhãn ngày đang chọn trên nút mở lịch (VD "Thứ tư, 24/09/2026")
        get selectedDateLabel() {
            if (!this.selectedDate) return 'Chọn ngày';
            return new Date(this.selectedDate + 'T00:00:00')
                .toLocaleDateString('vi-VN', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        // ─── Lịch tháng (Date Picker) ───
        // Khởi tạo tháng đang xem = tháng của ngày đang chọn (gọi 1 lần khi Alpine init)
        init() {
            const d = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : new Date();
            this.calMonth = d.getMonth();
            this.calYear = d.getFullYear();
        },

        get todayStr() {
            const n = new Date();
            return n.getFullYear() + '-' + String(n.getMonth() + 1).padStart(2, '0') + '-' + String(n.getDate()).padStart(2, '0');
        },

        get calendarLabel() {
            return 'Tháng ' + (this.calMonth + 1) + '/' + this.calYear;
        },

        // Tháng trước/tháng sau có được điều hướng không (giới hạn: tháng này → tháng sau)
        get canPrevMonth() {
            if (this.calYear === new Date().getFullYear()) return this.calMonth > new Date().getMonth();
            return this.calYear > new Date().getFullYear();
        },

        get canNextMonth() {
            const max = this.maxDate ? new Date(this.maxDate + 'T00:00:00') : null;
            if (!max) return true;
            if (this.calYear === max.getFullYear()) return this.calMonth < max.getMonth();
            return this.calYear < max.getFullYear();
        },

        prevMonth() {
            if (!this.canPrevMonth) return;
            this.calMonth--;
            if (this.calMonth < 0) { this.calMonth = 11; this.calYear--; }
        },

        nextMonth() {
            if (!this.canNextMonth) return;
            this.calMonth++;
            if (this.calMonth > 11) { this.calMonth = 0; this.calYear++; }
        },

        // Số ô trống đầu tháng (thứ 2 = đầu tuần)
        get firstDayOffset() {
            // getDay(): 0=CN..6=T7 → đổi về T2=0..CN=6
            const dow = new Date(this.calYear, this.calMonth, 1).getDay();
            return (dow + 6) % 7;
        },

        // Các ngày của tháng đang xem: {date, day, disabled} — disabled nếu ngoài cửa sổ đặt
        get calendarDays() {
            const days = new Date(this.calYear, this.calMonth + 1, 0).getDate();
            const min = this.todayStr, max = this.maxDate;
            const out = [];
            for (let i = 1; i <= days; i++) {
                const date = this.calYear + '-' + String(this.calMonth + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');
                out.push({ date, day: i, disabled: date < min || date > max });
            }
            return out;
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

        // Ước lượng giảm giá từ mã nhập vào (chỉ hiển thị — server tính lại khi submit)
        get promo() {
            let code = this.promoCode.trim().toUpperCase();
            return code ? (this.promotions[code] || null) : null;
        },

        get estimatedDiscount() {
            let promo = this.promo;
            if (!promo) return 0;
            let total = this.calculatedPrice;
            if (promo.min_amount && total < parseFloat(promo.min_amount)) return 0;
            let d = promo.discount_type === 'percent'
                ? Math.round(total * parseFloat(promo.discount_value) / 100)
                : parseFloat(promo.discount_value);
            return Math.min(d, total);
        },

        // Nút "Kiểm tra" mã giảm giá — báo hợp lệ/không, báo cả khi chưa đủ tiền tối thiểu
        checkPromo() {
            let code = this.promoCode.trim();
            if (!code) {
                this.promoValid = null;
                this.promoMessage = 'Nhập mã giảm giá trước đã.';
                return;
            }
            let promo = this.promotions[code.toUpperCase()];
            if (!promo) {
                this.promoValid = false;
                this.promoMessage = 'Mã "' + code + '" không tồn tại hoặc không áp dụng cho khu sân này.';
                return;
            }
            this.promoValid = true;
            let label = promo.discount_type === 'percent'
                ? 'giảm ' + parseFloat(promo.discount_value) + '%'
                : 'giảm ' + this.formatMoney(parseFloat(promo.discount_value)) + '/đơn';
            this.promoMessage = 'Mã hợp lệ — ' + label
                + (promo.min_amount ? ' (đơn từ ' + this.formatMoney(parseFloat(promo.min_amount)) + ')' : '')
                + '. Số tiền giảm sẽ hiện khi chọn khung giờ.';
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
