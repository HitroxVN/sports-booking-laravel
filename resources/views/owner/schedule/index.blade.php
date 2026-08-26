<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Lịch Biểu Sân Theo Ngày') }}
        </h2>
        <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-sm hover:bg-pink-50 hover:text-pink-600 transition">
            &larr; Trở về trang chủ
        </a>
    </div>
</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Thanh lọc Khu sân & Ngày -->
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-6 mb-8">
                <form method="GET" action="{{ route('owner.schedule.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn Khu Sân</label>
                        <select name="venue_id" onchange="this.form.submit()" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            @foreach($venues as $v)
                                <option value="{{ $v->id }}" @selected($selectedVenueId == $v->id)>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn Ngày Xem Lịch</label>
                        <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('owner.schedule.index', ['venue_id' => $selectedVenueId, 'date' => today()->format('Y-m-d')]) }}" class="px-5 py-2.5 bg-pink-100 text-pink-700 font-bold rounded-xl hover:bg-pink-200 transition text-sm">
                            Hôm nay
                        </a>
                        <span class="text-sm font-medium text-gray-500">
                            Đang xem: <strong class="text-gray-800">{{ $selectedDate->format('d/m/Y') }}</strong>
                        </span>
                    </div>
                </form>
            </div>

            <!-- Bảng Lịch Biểu Trực Quan -->
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden p-6">
                @if($courts->isEmpty())
                    <div class="text-center py-16 text-gray-500">
                        <p class="text-lg font-medium">Khu sân này chưa có sân con nào.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                                    <th class="p-4 text-left font-semibold w-40">Khung Giờ</th>
                                    @foreach($courts as $court)
                                        <th class="p-4 text-center font-bold text-base border-l border-pink-100">{{ $court->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pink-50">
                                @php
                                    // Tạo các mốc giờ từ 06:00 đến 22:00
                                    $timeSlots = [
                                        '06:00:00', '07:00:00', '08:00:00', '09:00:00', '10:00:00', '11:00:00',
                                        '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00',
                                        '18:00:00', '19:00:00', '20:00:00', '21:00:00', '22:00:00'
                                    ];
                                @endphp

                                @foreach($timeSlots as $index => $slotTime)
                                    @php
                                        if ($index + 1 >= count($timeSlots)) break;
                                        $nextSlotTime = $timeSlots[$index + 1];
                                    @endphp
                                    <tr class="hover:bg-pink-50/20 transition-colors">
                                        <td class="p-4 font-mono font-bold text-gray-600 bg-gray-50 text-sm">
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
                                            <td class="p-3 border-l border-pink-100 text-center align-middle">
                                                @if($matchedBooking)
                                                    <div class="bg-pink-500 text-white p-3 rounded-xl shadow-sm text-xs space-y-1">
                                                        <div class="font-bold truncate">#{{ $matchedBooking->code }}</div>
                                                        <div class="truncate">{{ $matchedBooking->user->name ?? 'Khách' }}</div>
                                                        <div>
                                                            @if($matchedBooking->isConfirmed())
                                                                <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold">Đã xác nhận</span>
                                                            @else
                                                                <span class="bg-yellow-400 text-gray-900 px-2 py-0.5 rounded text-[10px] font-bold">Chờ xử lý</span>
                                                            @endif
                                                        </div>
                                                        <div class="pt-1">
                                                            <a href="{{ route('owner.bookings.show', $matchedBooking) }}" class="text-[11px] underline font-semibold hover:text-pink-100">Xem chi tiết</a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-300 font-medium">Trống</span>
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
    </div>
</x-app-layout>