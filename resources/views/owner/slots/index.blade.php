<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Quản lý Khung Giờ') }} — {{ $court->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-white border-l-4 border-pink-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <div>
                    <a href="{{ route('owner.venues.courts.index', $court->venue) }}" class="text-sm font-bold text-pink-500 hover:text-pink-700 mb-2 inline-block">
                        &larr; Quay lại danh sách Sân Con
                    </a>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Cài đặt giá cho: {{ $court->name }}</h3>
                </div>
                <a href="{{ route('owner.courts.slots.create', $court) }}" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    + Thêm Khung Giờ
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                            <th class="p-5 font-semibold text-sm">Ngày trong tuần</th>
                            <th class="p-5 font-semibold text-sm text-center">Thời gian</th>
                            <th class="p-5 font-semibold text-sm text-right">Giá thường</th>
                            <th class="p-5 font-semibold text-sm text-center">Giờ vàng?</th>
                            <th class="p-5 font-semibold text-sm text-right">Giá giờ vàng</th>
                            <th class="p-5 font-semibold text-sm text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @php
                            $days = [0 => 'Chủ Nhật', 1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy'];
                        @endphp
                        @forelse($slots as $slot)
                        <tr class="hover:bg-pink-50/30 transition-colors">
                            <td class="p-5 font-bold text-gray-800">{{ $days[$slot->day_of_week] }}</td>
                            <td class="p-5 text-center font-mono text-gray-600">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            </td>
                            <td class="p-5 text-right font-medium text-gray-700">{{ number_format($slot->price, 0, ',', '.') }} đ</td>
                            <td class="p-5 text-center">
                                @if($slot->is_peak)
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Giờ Vàng</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-5 text-right font-medium text-pink-600">
                                {{ $slot->is_peak && $slot->peak_price ? number_format($slot->peak_price, 0, ',', '.') . ' đ' : '-' }}
                            </td>
                            <td class="p-5 text-right space-x-3 text-sm font-medium">
                                <form action="{{ route('owner.slots.destroy', $slot) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa khung giờ này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-400">Sân này chưa có khung giờ nào được thiết lập.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>