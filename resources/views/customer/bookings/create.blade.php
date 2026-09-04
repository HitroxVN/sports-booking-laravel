@extends('layouts.customer')

@section('title', 'Đặt sân: ' . $court->name)

@section('content')
    @php
        // Cấu hình truyền vào Alpine component bookingGrid (resources/js/CustomerBooking.js)
        $bookingConfig = [
            'initialDate'      => $dates[0]['full_date'],
            'slotCells'        => $slotCells,
            'existingBookings' => $existingBookings,
            'closures'         => $closures,
        ];
    @endphp
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8" x-data='bookingGrid(@json($bookingConfig))'>
        <div class="max-w-6xl mx-auto card-base p-6">
            
            <!-- Header Sân -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 mb-6 border-b border-zinc-200 dark:border-zinc-700">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Đặt sân: {{ $court->name }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Khu vực: {{ $court->venue->name ?? 'N/A' }} | Mặt sân: {{ $court->surface_type_name }}</p>
                </div>
                <!-- Chú thích -->
                <div class="flex items-center space-x-4 mt-3 md:mt-0 text-xs font-semibold">
                    <div class="flex items-center"><span class="w-3 h-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 inline-block mr-1 rounded"></span> Trống</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 inline-block mr-1 rounded"></span> Đã đặt</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-primary-600 inline-block mr-1 rounded"></span> Đang chọn</div>
                </div>
            </div>

            <!-- ═══ THÔNG TIN CHI TIẾT SÂN CON ═══ -->
            <div class="mb-8 rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 overflow-hidden">
                <div class="flex flex-col sm:flex-row">

                    {{-- Ảnh sân con --}}
                    <div class="sm:w-56 h-40 sm:h-auto flex-shrink-0 bg-zinc-200 dark:bg-zinc-700">
                        @if($court->image)
                            <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">
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
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $court->name }}</h3>
                                    <x-badge variant="info">{{ $court->sport->name ?? 'Môn thể thao' }}</x-badge>
                                    <x-badge :variant="$court->status === 'active' ? 'success' : 'warning'">
                                        {{ $court->status_name }}
                                    </x-badge>
                                </div>

                                {{-- Địa chỉ khu sân --}}
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 flex items-start gap-1 mb-3">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="text-[11px] text-zinc-400 dark:text-zinc-500 block">Giá thuê từ</span>
                                    <span class="text-xl font-extrabold text-primary-600 dark:text-primary-400">
                                        {{ number_format($minCourtPrice, 0, ',', '.') }}đ
                                        <span class="text-[11px] font-normal text-zinc-400 dark:text-zinc-500">/giờ</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Mô tả sân con --}}
                        @if($court->description)
                            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed mb-4 line-clamp-3">{{ $court->description }}</p>
                        @endif

                        {{-- Thông số: mặt sân, sức chứa + link về chi tiết khu sân --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs">
                            <span class="inline-flex items-center gap-1 text-zinc-600 dark:text-zinc-300">
                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mặt sân: <strong class="text-zinc-800 dark:text-zinc-200">{{ $court->surface_type_name }}</strong>
                            </span>
                            @if($court->max_players)
                                <span class="inline-flex items-center gap-1 text-zinc-600 dark:text-zinc-300">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Sức chứa: <strong class="text-zinc-800 dark:text-zinc-200">Tối đa {{ $court->max_players }} người</strong>
                                </span>
                            @endif
                            @if($court->venue)
                                <a href="{{ route('venues.show', $court->venue->slug) }}"
                                   class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:text-primary-700 font-semibold transition">
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
                <div class="p-4 mb-4 text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- 1. Thanh chọn Ngày trong tuần -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">1. Chọn ngày đá:</label>
                <div class="flex space-x-3 overflow-x-auto pb-3 pt-1 scrollbar-thin">
                    @foreach($dates as $d)
                        <button type="button"
                                @click="selectDate('{{ $d['full_date'] }}')"
                                :class="selectedDate === '{{ $d['full_date'] }}'
                                    ? 'bg-primary-600 border-primary-600 text-white'
                                    : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-primary-400'"
                                class="flex-1 min-w-[110px] flex-shrink-0 p-3 border rounded-xl text-center transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm">

                            <!-- Tên Thứ -->
                            <div class="text-xs uppercase font-bold tracking-wide"
                                 :class="selectedDate === '{{ $d['full_date'] }}' ? 'text-primary-100' : 'text-zinc-500 dark:text-zinc-400'">
                                {{ $d['day_name'] }}
                            </div>

                            <!-- Ngày/Tháng -->
                            <div class="text-base font-extrabold mt-1">
                                {{ $d['formatted'] }}
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Hướng dẫn chọn khoảng giờ -->
            <div class="mb-3 flex justify-between items-center">
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">2. Chọn khung giờ (Click giờ bắt đầu, sau đó click giờ kết thúc):</label>
                <button type="button" @click="resetSelection()" class="text-xs text-primary-600 dark:text-primary-400 hover:underline font-semibold" x-show="startSlotIdx !== null">
                    Đặt lại khoảng giờ
                </button>
            </div>

            <!-- 2. Khung Giờ Khả Dụng -->
            <div class="mb-6">
                <!-- Chủ sân chưa cài khung giờ cho ngày này -->
                <div x-show="hasNoSlots"
                     class="p-8 text-center rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                    <svg class="w-10 h-10 mx-auto mb-3 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-300">Sân chưa mở bán khung giờ nào cho ngày này</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Vui lòng chọn ngày khác hoặc liên hệ chủ sân để biết thêm thông tin.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3" x-show="!hasNoSlots">
                    <template x-for="(timeSlot, index) in availableSlots" :key="timeSlot.start">
                        <button type="button"
                                :disabled="isSlotBooked(timeSlot)"
                                @click="selectSlot(index)"
                                :class="isSlotBooked(timeSlot)
                                    ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-400 dark:text-red-500 cursor-not-allowed'
                                    : (isSlotSelected(index)
                                        ? 'bg-primary-600 border-primary-600 text-white'
                                        : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:border-primary-500 text-zinc-900 dark:text-zinc-100')"
                                class="p-3 border rounded-xl flex flex-col justify-between items-center transition-all h-20 focus:outline-none focus:ring-2 focus:ring-primary-500 cursor-pointer">

                            <!-- Giờ bắt đầu - Giờ kết thúc -->
                            <span class="text-sm font-bold"
                                  :class="isSlotBooked(timeSlot)
                                      ? 'text-red-400 dark:text-red-500'
                                      : (isSlotSelected(index)
                                          ? 'text-white'
                                          : 'text-zinc-900 dark:text-zinc-100')"
                                  x-text="timeSlot.start + ' - ' + timeSlot.end"></span>

                            <!-- Giá tiền / Trạng thái -->
                            <span class="text-xs font-semibold"
                                  :class="isSlotBooked(timeSlot)
                                      ? 'text-red-400 dark:text-red-500'
                                      : (isSlotSelected(index)
                                          ? 'text-primary-100'
                                          : 'text-primary-600 dark:text-primary-400')"
                                  x-text="slotBlockedReason(timeSlot) === 'closed' ? 'Ngoài khung' : (slotBlockedReason(timeSlot) === 'closure' ? 'Đã khóa' : (slotBlockedReason(timeSlot) === 'booked' ? 'Đã đặt' : (timeSlot.is_full_hour ? formatMoney(timeSlot.price) + '/h' : formatMoney(timeSlot.price))))"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Form Đặt Sân -->
            <form action="{{ route('customer.bookings.store') }}" method="POST" class="bg-zinc-50 dark:bg-zinc-800/50 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 mt-6">
                @csrf
                <input type="hidden" name="court_id" value="{{ $court->id }}">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="start_time" :value="selectedStart">
                <input type="hidden" name="end_time" :value="selectedEnd">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 block">Ngày đặt:</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 text-base" x-text="selectedDate"></span>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 block">Khung giờ chọn:</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 text-base" x-text="selectedStart ? (selectedStart + ' - ' + selectedEnd) : 'Chưa chọn'"></span>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 block">Tổng tiền tạm tính:</span>
                        <span class="text-xl font-extrabold text-primary-600 dark:text-primary-400" x-text="formatMoney(calculatedPrice)"></span>
                    </div>
                    <div class="text-right">
                        <button type="submit"
                                :disabled="!selectedStart"
                                :class="!selectedStart
                                    ? 'bg-zinc-300 dark:bg-zinc-700 text-zinc-500 cursor-not-allowed'
                                    : 'btn-primary'"
                                class="w-full py-3 px-4 font-bold rounded-xl transition-all shadow">
                            Xác nhận đặt sân
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection