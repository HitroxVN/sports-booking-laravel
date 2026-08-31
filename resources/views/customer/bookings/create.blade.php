<x-app-layout>
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8" x-data="bookingGrid()">
        <div class="max-w-6xl mx-auto bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <!-- Header Sân -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 mb-6 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Đặt sân: {{ $court->name }}</h2>
                    <p class="text-sm text-gray-500">Khu vực: {{ $court->venue->name ?? 'N/A' }} | Mặt sân: {{ $court->surface_type_name }}</p>
                </div>
                <!-- Chú thích -->
                <div class="flex items-center space-x-4 mt-3 md:mt-0 text-xs font-semibold">
                    <div class="flex items-center"><span class="w-3 h-3 bg-white border border-gray-400 inline-block mr-1 rounded"></span> Trống</div>
                    <div class="flex items-center"><span class="w-3 h-3 border border-red-300 inline-block mr-1 rounded" style="background-color: #fee2e2;"></span> Đã đặt</div>
                    <div class="flex items-center"><span class="w-3 h-3 inline-block mr-1 rounded" style="background-color: #4f46e5;"></span> Đang chọn</div>
                </div>
            </div>

            <!-- ═══ THÔNG TIN CHI TIẾT SÂN CON ═══ -->
            <div class="mb-8 rounded-2xl border border-gray-200 bg-gray-50/60 overflow-hidden">
                <div class="flex flex-col sm:flex-row">

                    {{-- Ảnh sân con --}}
                    <div class="sm:w-56 h-40 sm:h-auto flex-shrink-0 bg-gray-200">
                        @if($court->image)
                            <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Nội dung thông tin --}}
                    <div class="flex-1 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div>
                                {{-- Tên sân + badge môn thể thao --}}
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $court->name }}</h3>
                                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                        {{ $court->sport->name ?? 'Môn thể thao' }}
                                    </span>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full
                                        {{ $court->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $court->status_name }}
                                    </span>
                                </div>

                                {{-- Địa chỉ khu sân --}}
                                <p class="text-xs text-gray-500 flex items-start gap-1 mb-3">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $court->venue->address ?? '' }}, {{ $court->venue->district ?? '' }}, {{ $court->venue->city ?? '' }}</span>
                                </p>
                            </div>

                            {{-- Giá từ --}}
                            @php $minCourtPrice = $court->slots->min('price'); @endphp
                            @if($minCourtPrice)
                                <div class="text-left sm:text-right flex-shrink-0">
                                    <span class="text-[11px] text-gray-400 block">Giá thuê từ</span>
                                    <span class="text-xl font-extrabold text-indigo-600">
                                        {{ number_format($minCourtPrice, 0, ',', '.') }}đ
                                        <span class="text-[11px] font-normal text-gray-400">/giờ</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Mô tả sân con --}}
                        @if($court->description)
                            <p class="text-xs text-gray-600 leading-relaxed mb-4 line-clamp-3">{{ $court->description }}</p>
                        @endif

                        {{-- Thông số: mặt sân, sức chứa + link về chi tiết khu sân --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs">
                            <span class="inline-flex items-center gap-1 text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mặt sân: <strong class="text-gray-800">{{ $court->surface_type_name }}</strong>
                            </span>
                            @if($court->max_players)
                                <span class="inline-flex items-center gap-1 text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Sức chứa: <strong class="text-gray-800">Tối đa {{ $court->max_players }} người</strong>
                                </span>
                            @endif
                            @if($court->venue)
                                <a href="{{ route('venues.show', $court->venue->slug) }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                    Xem chi tiết khu sân
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
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
                                  x-text="timeSlot.isClosed ? 'Đã khóa' : (timeSlot.isBooked ? 'Đã đặt' : formatMoney(timeSlot.price) + '/h')"></span>
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
                closures: JSON.parse('{!! json_encode($closures) !!}'),
                
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

                        // Sân bị khóa lịch trong ngày này: khóa cả ngày (start_time null) hoặc trùng khung giờ
                        let isClosed = this.closures.some(c => {
                            if (c.date !== this.selectedDate) return false;
                            if (!c.start_time) return true; // khóa cả ngày
                            let cStart = c.start_time.substring(0, 5);
                            let cEnd = c.end_time.substring(0, 5);
                            return (startStr < cEnd && endStr > cStart);
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
                            isBooked: isBooked || isClosed,
                            isClosed: isClosed
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