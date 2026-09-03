<x-owner-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ __('Lịch Biểu Sân Theo Ngày') }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Xem lịch đặt theo khung giờ của từng sân con</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" class="btn-secondary text-xs shrink-0">
                &larr; Trở về trang chủ
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Thanh lọc Khu sân & Ngày -->
        <div class="card-base p-6">
            <form method="GET" action="{{ route('owner.schedule.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label for="schedule-venue" class="label-eyebrow block mb-2">Chọn Khu Sân</label>
                    <select id="schedule-venue" name="venue_id" onchange="this.form.submit()" class="input-base">
                        @foreach($venues as $v)
                            <option value="{{ $v->id }}" @selected($selectedVenueId == $v->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="schedule-date" class="label-eyebrow block mb-2">Chọn Ngày Xem Lịch</label>
                    <input id="schedule-date" type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()" class="input-base">
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('owner.schedule.index', ['venue_id' => $selectedVenueId, 'date' => today()->format('Y-m-d')]) }}" class="btn-secondary text-sm">
                        Hôm nay
                    </a>
                    <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        Đang xem: <strong class="text-zinc-900 dark:text-zinc-100">{{ $selectedDate->format('d/m/Y') }}</strong>
                    </span>
                </div>
            </form>
        </div>

        <!-- Bảng Lịch Biểu Trực Quan -->
        <div class="card-base p-6">
            @if($courts->isEmpty())
                <x-empty-state icon="🏟️" title="Khu sân này chưa có sân con nào" description="Thêm sân con để xem lịch biểu theo khung giờ." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                                <th class="p-4 text-left font-semibold text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 w-40">Khung Giờ</th>
                                @foreach($courts as $court)
                                    <th class="p-4 text-center font-semibold text-sm text-zinc-900 dark:text-zinc-100 border-l border-zinc-200 dark:border-zinc-800">{{ $court->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($timeSlots as $index => $slotTime)
                                @php
                                    if ($index + 1 >= count($timeSlots)) break;
                                    $nextSlotTime = $timeSlots[$index + 1];
                                @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="p-4 font-mono font-semibold text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50 text-sm">
                                        {{ substr($slotTime, 0, 5) }} - {{ substr($nextSlotTime, 0, 5) }}
                                    </td>

                                    @foreach($courts as $court)
                                        @php
                                            // Tìm đơn đặt sân khớp với sân này và khung giờ này
                                            $matchedBooking = $bookings->first(function($b) use ($court, $slotTime, $nextSlotTime) {
                                                return $b->court_id == $court->id
                                                    && $b->start_time <= $slotTime
                                                    && $b->end_time >= $nextSlotTime;
                                            });
                                        @endphp
                                        <td class="p-3 border-l border-zinc-200 dark:border-zinc-800 text-center align-middle">
                                            @if($matchedBooking)
                                                <div class="bg-primary-600 dark:bg-primary-700 text-white p-3 rounded-xl shadow-sm text-xs space-y-1">
                                                    <div class="font-bold truncate">#{{ $matchedBooking->code }}</div>
                                                    <div class="truncate">{{ $matchedBooking->user->name ?? 'Khách' }}</div>
                                                    <div>
                                                        @if($matchedBooking->isConfirmed())
                                                            <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold">Đã xác nhận</span>
                                                        @else
                                                            <span class="bg-amber-400 text-zinc-900 px-2 py-0.5 rounded text-[10px] font-bold">Chờ xử lý</span>
                                                        @endif
                                                    </div>
                                                    <div class="pt-1">
                                                        <a href="{{ route('owner.bookings.show', $matchedBooking) }}" class="text-[11px] underline font-semibold hover:text-primary-100">Xem chi tiết</a>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-zinc-300 dark:text-zinc-600 font-medium">Trống</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-owner-layout>
