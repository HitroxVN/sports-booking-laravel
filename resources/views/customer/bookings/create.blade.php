<x-app-layout>
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8" x-data="bookingGrid()">
        <div class="max-w-6xl mx-auto bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <!-- Header Sân -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 mb-6 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Đặt sân: {{ $court->name }}</h2>
                    <p class="text-sm text-gray-500">Khu vực: {{ $court->venue->name ?? 'N/A' }} | Mặt sân: {{ $court->surface_type }}</p>
                </div>
                <!-- Chú thích -->
                <div class="flex items-center space-x-4 mt-3 md:mt-0 text-xs font-semibold">
                    <div class="flex items-center"><span class="w-3 h-3 bg-white border border-gray-400 inline-block mr-1 rounded"></span> Trống</div>
                    <div class="flex items-center"><span class="w-3 h-3 border border-red-300 inline-block mr-1 rounded" style="background-color: #fee2e2;"></span> Đã đặt</div>
                    <div class="flex items-center"><span class="w-3 h-3 inline-block mr-1 rounded" style="background-color: #4f46e5;"></span> Đang chọn</div>
                </div>
            </div>

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- 1. Thanh chọn Ngày trong tuần -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">1. Chọn ngày đá:</label>
                <div class="flex space-x-3 overflow-x-auto pb-3 pt-1 scrollbar-thin">
                    @foreach($dates as $d)
                        <button type="button" 
                                @click="selectDate('{{ $d['full_date'] }}')"
                                :style="selectedDate === '{{ $d['full_date'] }}' 
                                    ? 'background-color: #4f46e5 !important; color: #ffffff !important; border-color: #4f46e5 !important;' 
                                    : 'background-color: #ffffff !important; color: #374151 !important; border-color: #d1d5db !important;'"
                                class="flex-1 min-w-[110px] flex-shrink-0 p-3 border rounded-xl text-center transition-all cursor-pointer focus:outline-none shadow-sm">
                            
                            <!-- Tên Thứ -->
                            <div class="text-xs uppercase font-bold tracking-wide"
                                 :style="selectedDate === '{{ $d['full_date'] }}' ? 'color: #ffffff !important;' : 'color: #6b7280 !important;'">
                                {{ $d['day_name'] }}
                            </div>
                            
                            <!-- Ngày/Tháng -->
                            <div class="text-base font-extrabold mt-1"
                                 :style="selectedDate === '{{ $d['full_date'] }}' ? 'color: #ffffff !important;' : 'color: #1f2937 !important;'">
                                {{ $d['formatted'] }}
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Hướng dẫn chọn khoảng giờ -->
            <div class="mb-3 flex justify-between items-center">
                <label class="block text-sm font-semibold text-gray-700">2. Chọn khung giờ (Click giờ bắt đầu, sau đó click giờ kết thúc):</label>
                <button type="button" @click="resetSelection()" class="text-xs text-indigo-600 hover:underline font-semibold" x-show="startSlotIdx !== null">
                    Đặt lại khoảng giờ
                </button>
            </div>

            <!-- 2. Khung Giờ Khả Dụng -->
            <div class="mb-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <template x-for="(timeSlot, index) in availableSlots" :key="timeSlot.start">
                        <button type="button"
                                :disabled="timeSlot.isBooked"
                                @click="selectSlot(index)"
                                :style="timeSlot.isBooked 
                                    ? 'background-color: #fee2e2 !important; border-color: #fca5a5 !important; cursor: not-allowed !important;' 
                                    : (isSlotSelected(index) 
                                        ? 'background-color: #4f46e5 !important; border-color: #4f46e5 !important;' 
                                        : 'background-color: #ffffff !important; border-color: #d1d5db !important;')"
                                class="p-3 border rounded-xl flex flex-col justify-between items-center transition-all h-20 focus:outline-none cursor-pointer">
                            
                            <!-- Giờ bắt đầu - Giờ kết thúc -->
                            <span class="text-sm font-bold" 
                                  :style="timeSlot.isBooked 
                                      ? 'color: #dc2626 !important;' 
                                      : (isSlotSelected(index) 
                                          ? 'color: #ffffff !important;' 
                                          : 'color: #1f2937 !important;')"
                                  x-text="timeSlot.start + ' - ' + timeSlot.end"></span>
                            
                            <!-- Giá tiền / Trạng thái -->
                            <span class="text-xs font-semibold" 
                                  :style="timeSlot.isBooked 
                                      ? 'color: #ef4444 !important;' 
                                      : (isSlotSelected(index) 
                                          ? 'color: #e0e7ff !important;' 
                                          : 'color: #4f46e5 !important;')" 
                                  x-text="timeSlot.isBooked ? 'Đã đặt' : formatMoney(timeSlot.price) + '/h'"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Form Đặt Sân -->
            <form action="{{ route('customer.bookings.store') }}" method="POST" class="bg-gray-50 p-6 rounded-xl border mt-6">
                @csrf
                <input type="hidden" name="court_id" value="{{ $court->id }}">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="start_time" :value="selectedStart">
                <input type="hidden" name="end_time" :value="selectedEnd">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <div>
                        <span class="text-xs text-gray-500 block">Ngày đặt:</span>
                        <span class="font-bold text-gray-800 text-base" x-text="selectedDate"></span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Khung giờ chọn:</span>
                        <span class="font-bold text-gray-800 text-base" x-text="selectedStart ? (selectedStart + ' - ' + selectedEnd) : 'Chưa chọn'"></span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Tổng tiền tạm tính:</span>
                        <span class="text-xl font-extrabold text-indigo-600" x-text="formatMoney(calculatedPrice)"></span>
                    </div>
                    <div class="text-right">
                        <button type="submit" 
                                :disabled="!selectedStart"
                                :style="!selectedStart 
                                    ? 'background-color: #9ca3af !important; color: #ffffff !important; cursor: not-allowed !important;' 
                                    : 'background-color: #4f46e5 !important; color: #ffffff !important; cursor: pointer !important;'"
                                class="w-full py-3 px-4 font-bold rounded-lg transition-all shadow">
                            Xác nhận đặt sân
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js xử lý chọn khoảng giờ -->
    <script>
        function bookingGrid() {
            return {
                selectedDate: '{{ $dates[0]["full_date"] }}',
                startSlotIdx: null,
                endSlotIdx: null,
                courtSlots: JSON.parse('{!! json_encode($court->slots) !!}'),
                existingBookings: JSON.parse('{!! json_encode($existingBookings) !!}'),
                
                get availableSlots() {
                    let slots = [];
                    for (let hour = 5; hour < 22; hour++) {
                        let startStr = (hour < 10 ? '0' : '') + hour + ':00';
                        let endStr = (hour + 1 < 10 ? '0' : '') + (hour + 1) + ':00';
                        
                        let isBooked = this.existingBookings.some(b => {
                            if (b.booking_date !== this.selectedDate) return false;
                            let bStart = b.start_time.substring(0, 5);
                            let bEnd = b.end_time.substring(0, 5);
                            return (startStr < bEnd && endStr > bStart);
                        });

                        let matchingSlot = this.courtSlots.find(s => {
                            let sStart = s.start_time.substring(0, 5);
                            let sEnd = s.end_time.substring(0, 5);
                            return startStr >= sStart && endStr <= sEnd;
                        });

                        let price = 100000;
                        if (matchingSlot) {
                            price = (matchingSlot.is_peak && matchingSlot.peak_price) ? parseFloat(matchingSlot.peak_price) : parseFloat(matchingSlot.price);
                        }

                        slots.push({
                            start: startStr,
                            end: endStr,
                            price: price,
                            isBooked: isBooked
                        });
                    }
                    return slots;
                },

                selectSlot(idx) {
                    if (this.availableSlots[idx].isBooked) return;

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
                    if (this.startSlotIdx === null) return '';
                    let start = Math.min(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
                    return this.availableSlots[start].start;
                },

                get selectedEnd() {
                    if (this.startSlotIdx === null) return '';
                    let end = Math.max(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
                    return this.availableSlots[end].end;
                },

                get calculatedPrice() {
                    if (this.startSlotIdx === null) return 0;
                    let start = Math.min(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
                    let end = Math.max(this.startSlotIdx, this.endSlotIdx !== null ? this.endSlotIdx : this.startSlotIdx);
                    
                    let total = 0;
                    for (let i = start; i <= end; i++) {
                        total += this.availableSlots[i].price;
                    }
                    return total;
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                }
            }
        }
    </script>
</x-app-layout>