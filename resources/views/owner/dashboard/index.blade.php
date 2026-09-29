<x-owner-layout>
    <div x-data="{
        openQuickBookingModal: false,
        courtId: '{{ $courts->first()->id ?? '' }}',
        bookingDate: '{{ today()->toDateString() }}',
        startTime: '17:00',
        endTime: '18:30',
        duration: 90,
        hourlyRate: {{ (float) ($courts->first()->default_price_per_hour ?? 0) }},
        totalAmount: {{ (float) (($courts->first()->default_price_per_hour ?? 0) * 1.5) }},
        customerName: 'Khách vãng lai',
        customerPhone: '',
        status: 'confirmed',
        paymentMethod: 'at_venue',
        paymentStatus: 'unpaid',
        updateCourtPrice(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            this.hourlyRate = parseFloat(selectedOption.getAttribute('data-price') || 0);
            this.recalcTotal();
        },
        setDuration(mins) {
            this.duration = mins;
            if (this.startTime) {
                const parts = this.startTime.split(':');
                const startMins = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                const endMins = startMins + mins;
                const endH = Math.floor(endMins / 60) % 24;
                const endM = endMins % 60;
                this.endTime = (endH < 10 ? '0' : '') + endH + ':' + (endM < 10 ? '0' : '') + endM;
            }
            this.recalcTotal();
        },
        updateEndTime() {
            if (this.startTime && this.duration) {
                this.setDuration(this.duration);
            }
        },
        recalcTotal() {
            if (this.hourlyRate > 0 && this.duration > 0) {
                this.totalAmount = Math.round((this.hourlyRate * this.duration) / 60);
            }
        }
    }">

        <!-- Header Slot -->
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                        Tổng Quan Hoạt Động
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                        Xin chào, <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</span>! Tình hình vận hành sân và doanh thu hôm nay.
                    </p>
                </div>

                <!-- Nút Tác Vụ Nhanh (Quick Action Header) -->
                <div class="flex items-center gap-3">
                    <button @click="openQuickBookingModal = true"
                        type="button"
                        class="btn-primary flex items-center gap-2 px-4 py-2.5 shadow-sm shadow-primary-600/30 hover:shadow-md hover:shadow-primary-600/20 transition-all font-semibold text-sm">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>+ Tạo đơn mới</span>
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="max-w-7xl mx-auto space-y-6">

            <!-- ── Bộ Thẻ Thống Kê Vận Hành (Operational Stats Cards) ── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">

                <!-- Thẻ 1: Doanh thu hôm nay -->
                <div class="card-base p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-300">
                            Doanh Thu Hôm Nay
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">
                            {{ number_format((float) ($todayRevenue ?? 0), 0, ',', '.') }} <span class="text-base font-semibold text-zinc-500 dark:text-zinc-300">đ</span>
                        </div>
                        <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                            @if($revenueTrendPercent > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 ring-1 ring-emerald-600/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    +{{ $revenueTrendPercent }}%
                                </span>
                            @elseif($revenueTrendPercent < 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 ring-1 ring-rose-600/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                    {{ $revenueTrendPercent }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/50 dark:border-zinc-700/50">
                                    0%
                                </span>
                            @endif
                            <span class="text-xs text-zinc-500 dark:text-zinc-300 font-medium">so với hôm qua ({{ number_format((float) ($yesterdayRevenue ?? 0), 0, ',', '.') }} đ)</span>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 2: Lượt đặt hôm nay -->
                <div class="card-base p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-300">
                            Lượt Đặt Hôm Nay
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/60 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5M9.75 14.25l2.25 2.25 4.5-4.5" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">
                            {{ $todayBookingsCount }} <span class="text-base font-semibold text-zinc-500 dark:text-zinc-300">ca</span>
                        </div>
                        <div class="mt-2 flex items-center text-xs text-zinc-600 dark:text-zinc-300 font-medium">
                            @if($ongoingMatchesCount > 0)
                                <span class="relative flex h-2 w-2 mr-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $ongoingMatchesCount }}</span>&nbsp;<span>trận đang diễn ra</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600 mr-1.5"></span>
                                <span>0 trận đang diễn ra</span>
                            @endif
                        </div>
                    </div>
                </div>

        <!-- Bảng danh sách đơn đặt sân gần đây -->
        <div class="card-base">
            <div class="flex justify-between items-center px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Đơn Đặt Sân Gần Đây</h3>
                <a href="{{ route('owner.bookings.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">Xem tất cả &rarr;</a>
                <!-- Thẻ 3: Hiệu suất kín sân -->
                <div class="card-base p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-300">
                            Hiệu Suất Kín Sân
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-800/60 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">
                            {{ $occupancyRate }}<span class="text-base font-semibold text-zinc-500 dark:text-zinc-300">%</span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-purple-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $occupancyRate) }}%"></div>
                            </div>
                            <div class="mt-1.5 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-300 font-medium">
                                <span>{{ $bookedHours }}h đã đặt</span>
                                <span>{{ $totalCapacityHours }}h khả dụng</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 4: Chờ xác nhận -->
                <div class="card-base p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-300">
                            Chờ Xác Nhận
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/60 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-500 dark:text-amber-400 tracking-tight">
                            {{ $pendingBookingsCount }} <span class="text-base font-semibold text-zinc-500 dark:text-zinc-300">đơn</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('owner.bookings.index', ['status' => 'pending']) }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-200 hover:underline transition-colors">
                                <span>Xem danh sách cần duyệt</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Tối Ưu Bố Cục Khu Vực Dưới (Two-Column Layout 7:3) ── -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Cột Trái: Lịch Thi Đấu Hôm Nay / Next Up (approx 70% desktop) -->
                <div class="lg:col-span-8 card-base p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">
                                    Lịch Thi Đấu Hôm Nay
                                </h2>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-50 text-primary-700 dark:bg-primary-950/80 dark:text-primary-300 border border-primary-200/70 dark:border-primary-800/70">
                                    {{ $todayMatches->count() }} trận
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-300 mt-0.5 font-normal">
                                Các ca đấu sắp diễn ra trong ngày theo thứ tự 24 giờ. Thao tác Check-in / Xác nhận nhanh trực tiếp.
                            </p>
                        </div>
                        <a href="{{ route('owner.bookings.index') }}"
                            class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline inline-flex items-center gap-1 shrink-0">
                            Xem tất cả đơn &rarr;
                        </a>
                    </div>

                    <!-- Danh sách trận đấu hôm nay -->
                    <div class="space-y-3">
                        @forelse($todayMatches as $match)
                            <div class="p-3.5 sm:p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-50/70 dark:bg-zinc-800/50 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/80 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                                
                                <!-- Khối thời gian và thông tin trận đấu -->
                                <div class="flex items-start gap-3.5 min-w-0">
                                    <!-- Badge 24h Time -->
                                    <div class="shrink-0 text-center px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm min-w-[5rem]">
                                        <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-50 font-mono">
                                            {{ \Carbon\Carbon::parse($match->start_time)->format('H:i') }}
                                        </span>
                                        <span class="block text-[11px] font-medium text-zinc-500 dark:text-zinc-300 font-mono">
                                            đến {{ \Carbon\Carbon::parse($match->end_time)->format('H:i') }}
                                        </span>
                                    </div>

                                    <!-- Chi tiết sân & khách hàng -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm sm:text-base">
                                                {{ $match->court->name }}
                                            </span>
                                            @if($match->court->sport)
                                                <span class="badge bg-zinc-200/80 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-300/50 dark:border-zinc-600/50 font-medium">
                                                    {{ $match->court->sport->name }}
                                                </span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-md bg-zinc-200/70 dark:bg-zinc-700/70 text-zinc-700 dark:text-zinc-200 font-mono text-xs font-medium border border-zinc-300/60 dark:border-zinc-600/60">
                                                #{{ $match->code }}
                                            </span>
                                        </div>

                                        <div class="mt-1.5 flex items-center gap-3 text-xs text-zinc-600 dark:text-zinc-300 flex-wrap">
                                            <span class="flex items-center gap-1 font-semibold text-zinc-900 dark:text-zinc-100">
                                                <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $match->user->name ?? 'Khách vãng lai' }}
                                            </span>
                                            @if($match->user?->phone)
                                                <span class="flex items-center gap-1 font-medium text-zinc-700 dark:text-zinc-200">
                                                    <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    {{ $match->user->phone }}
                                                </span>
                                            @endif
                                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">• {{ $match->court->venue->name ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trạng thái & Nút thao tác nhanh trực tiếp -->
                                <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-zinc-200/70 dark:border-zinc-700">
                                    
                                    <!-- Nhãn trạng thái -->
                                    <div>
                                        @if($match->isPending())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/80 dark:text-amber-300 dark:border-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Chờ duyệt
                                            </span>
                                        @elseif($match->isConfirmed())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/80 dark:text-blue-300 dark:border-blue-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Đã xác nhận
                                            </span>
                                        @elseif($match->isCompleted())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Hoàn thành
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick action button -->
                                    <div class="flex items-center gap-1.5">
                                        @if($match->isPending())
                                            <form method="POST" action="{{ route('owner.bookings.update', $match) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit"
                                                    title="Xác nhận ca đặt sân này ngay"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>Xác nhận</span>
                                                </button>
                                            </form>
                                        @elseif($match->isConfirmed())
                                            <form method="POST" action="{{ route('owner.bookings.update', $match) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit"
                                                    title="Check-in khách vào sân thi đấu"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Check-in</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs text-zinc-700 dark:text-zinc-200 font-semibold">
                                                <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Đã nhận sân
                                            </span>
                                        @endif

                                        <a href="{{ route('owner.bookings.show', $match) }}"
                                            class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-200/60 dark:hover:bg-zinc-700 transition-colors"
                                            title="Xem chi tiết đơn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>

                                </div>

                            </div>
                        @empty
                            <div class="py-12 px-4 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">
                                    Chưa có lịch thi đấu nào hôm nay
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-300 mt-1 max-w-sm mx-auto">
                                    Hiện chưa có ca đặt sân nào diễn ra trong ngày. Bạn có thể tạo đơn mới cho khách gọi điện hoặc khách vãng lai.
                                </p>
                                <button @click="openQuickBookingModal = true"
                                    type="button"
                                    class="mt-4 btn-primary text-xs py-2 px-3.5 inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>Tạo đơn đặt ngay</span>
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cột Phải: Biểu Đồ Doanh Thu 7 Ngày (approx 30% desktop) -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="card-base p-5 sm:p-6">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                            <div>
                                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">
                                    Doanh Thu 7 Ngày
                                </h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-300 mt-0.5 font-normal">
                                    Biến động doanh thu tuần qua
                                </p>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v16.5a1.5 1.5 0 0 0 1.5 1.5h16.5M7.5 17v-4.5m4.5 4.5V9m4.5 8V5" />
                                </svg>
                            </div>
                        </div>

                        <!-- Mini stats summary -->
                        <div class="mt-4 grid grid-cols-2 gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-300 block">Tổng 7 ngày</span>
                                <span class="text-sm font-extrabold text-zinc-900 dark:text-zinc-50 mt-0.5 block truncate">
                                    {{ number_format((float) ($weekTotalRevenue ?? 0), 0, ',', '.') }} đ
                                </span>
                            </div>
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-300 block">Trung bình/ngày</span>
                                <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 block truncate">
                                    {{ number_format((float) ($weekAvgDaily ?? 0), 0, ',', '.') }} đ
                                </span>
                            </div>
                        </div>

                        <!-- Chart Canvas Container -->
                        <div class="mt-4 relative h-56 w-full">
                            <canvas id="weeklyRevenueChart"></canvas>
                        </div>

                        <!-- Link to full report -->
                        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-center">
                            <a href="{{ route('owner.reports.index') }}"
                                class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 inline-flex items-center gap-1 transition-colors hover:underline">
                                <span>Xem báo cáo doanh thu chi tiết</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ── Modal Tạo Đơn Nhanh Cho Khách Vãng Lai / Gọi Điện ── -->
        <div x-show="openQuickBookingModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="openQuickBookingModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm transition-opacity"
                @click="openQuickBookingModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="openQuickBookingModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @keydown.escape.window="openQuickBookingModal = false"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <!-- Header Modal -->
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50" id="modal-title">
                                    Tạo Đơn Đặt Sân Nhanh
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-300">
                                    Dành cho khách vãng lai, gọi điện thoại hoặc đặt trực tiếp tại sân
                                </p>
                            </div>
                        </div>
                        <button @click="openQuickBookingModal = false"
                            type="button"
                            class="text-zinc-400 hover:text-zinc-600 dark:text-zinc-400 dark:hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('owner.bookings.quick-store') }}" class="p-6 space-y-4">
                        @csrf

                        <!-- Chọn Sân -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                Sân Đấu <span class="text-red-500">*</span>
                            </label>
                            <select name="court_id"
                                x-model="courtId"
                                @change="updateCourtPrice($event)"
                                required
                                class="input-base">
                                @forelse($venues as $venue)
                                    <optgroup label="{{ $venue->name }}">
                                        @foreach($venue->courts as $court)
                                            <option value="{{ $court->id }}" data-price="{{ $court->default_price_per_hour }}">
                                                {{ $court->name }} ({{ number_format((float) ($court->default_price_per_hour ?? 0), 0, ',', '.') }} đ/h)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @empty
                                    <option value="">Chưa có sân nào</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Ngày & Thời gian đặt sân -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Ngày Đặt <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                    name="booking_date"
                                    x-model="bookingDate"
                                    required
                                    class="input-base">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Giờ Bắt Đầu <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                    name="start_time"
                                    x-model="startTime"
                                    @change="updateEndTime()"
                                    required
                                    class="input-base">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Giờ Kết Thúc <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                    name="end_time"
                                    x-model="endTime"
                                    required
                                    class="input-base">
                            </div>
                        </div>

                        <!-- Thời lượng nhanh -->
                        <div class="flex items-center gap-2 pt-1">
                            <span class="text-xs text-zinc-600 dark:text-zinc-300 font-medium">Chọn nhanh thời lượng:</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                    @click="setDuration(60)"
                                    :class="duration === 60 ? 'bg-primary-600 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200/60 dark:border-zinc-700/60'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition-colors">
                                    60 phút
                                </button>
                                <button type="button"
                                    @click="setDuration(90)"
                                    :class="duration === 90 ? 'bg-primary-600 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200/60 dark:border-zinc-700/60'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition-colors">
                                    90 phút
                                </button>
                                <button type="button"
                                    @click="setDuration(120)"
                                    :class="duration === 120 ? 'bg-primary-600 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200/60 dark:border-zinc-700/60'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition-colors">
                                    120 phút
                                </button>
                            </div>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Tên Khách Hàng
                                </label>
                                <input type="text"
                                    name="customer_name"
                                    x-model="customerName"
                                    placeholder="Khách vãng lai / Anh Nam"
                                    class="input-base">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Số Điện Thoại
                                </label>
                                <input type="text"
                                    name="customer_phone"
                                    x-model="customerPhone"
                                    placeholder="0912345678"
                                    class="input-base">
                            </div>
                        </div>

                        <!-- Giá tiền & Trạng thái thanh toán -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Tổng Tiền (VNĐ)
                                </label>
                                <input type="number"
                                    name="total_amount"
                                    x-model="totalAmount"
                                    min="0"
                                    step="1000"
                                    class="input-base">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Trạng Thái Đơn
                                </label>
                                <select name="status" x-model="status" class="input-base">
                                    <option value="confirmed">Đã xác nhận (Khách nhận sân)</option>
                                    <option value="pending">Chờ xử lý (Giữ chỗ)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Phương Thức Thanh Toán
                                </label>
                                <select name="payment_method" x-model="paymentMethod" class="input-base">
                                    <option value="at_venue">Thanh toán tại sân (Tiền mặt/POS)</option>
                                    <option value="full_online">Chuyển khoản trực tuyến</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                    Trạng Thái Thanh Toán
                                </label>
                                <select name="payment_status" x-model="paymentStatus" class="input-base">
                                    <option value="unpaid">Chưa thanh toán</option>
                                    <option value="fully_paid">Đã thanh toán đủ</option>
                                    <option value="deposit_paid">Đã cọc</option>
                                </select>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200 mb-1.5">
                                Ghi Chú
                            </label>
                            <input type="text"
                                name="notes"
                                placeholder="Ví dụ: Gọi điện qua hotline lúc 15h, mượn bóng số 5..."
                                class="input-base">
                        </div>

                        <!-- Nút hành động -->
                        <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-3">
                            <button @click="openQuickBookingModal = false"
                                type="button"
                                class="btn-secondary text-sm">
                                Hủy Bỏ
                            </button>
                            <button type="submit"
                                class="btn-primary text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Tạo Đơn Đặt Sân</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js CDN & Mini Chart Render Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const canvas = document.getElementById('weeklyRevenueChart');
            if (!canvas) return;

            const isDark = () => document.documentElement.classList.contains('dark');

            const getColors = () => {
                const dark = isDark();
                return {
                    textColor: dark ? '#e2e8f0' : '#4b5563',
                    gridColor: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)',
                    barBg: dark ? 'rgba(16, 185, 129, 0.75)' : 'rgba(16, 185, 129, 0.85)',
                    barHoverBg: dark ? '#34d399' : '#059669',
                };
            };

            const colors = getColors();
            const ctx = canvas.getContext('2d');

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: {!! json_encode($chartValues) !!},
                        backgroundColor: colors.barBg,
                        hoverBackgroundColor: colors.barHoverBg,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 32,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark() ? '#18181b' : '#1f2937',
                            titleColor: '#ffffff',
                            bodyColor: '#f1f5f9',
                            borderColor: isDark() ? '#52525b' : '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: colors.textColor,
                                font: { size: 11, weight: 600 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colors.gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.textColor,
                                font: { size: 10, weight: 600 },
                                callback: function(value) {
                                    return value >= 1000000 ? (value / 1000000) + 'M' : (value >= 1000 ? (value / 1000) + 'k' : value);
                                }
                            }
                        }
                    }
                }
            });

            // Lắng nghe sự kiện chuyển đổi theme (Light / Dark Mode)
            window.addEventListener('theme-changed', function (e) {
                const newColors = getColors();
                chart.data.datasets[0].backgroundColor = newColors.barBg;
                chart.data.datasets[0].hoverBackgroundColor = newColors.barHoverBg;
                chart.options.scales.x.ticks.color = newColors.textColor;
                chart.options.scales.y.ticks.color = newColors.textColor;
                chart.options.scales.y.grid.color = newColors.gridColor;
                chart.update();
            });
        });
    </script>
</x-owner-layout>
