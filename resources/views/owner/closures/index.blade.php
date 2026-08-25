<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Quản lý Khóa Lịch') }} — {{ $court->name }}
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
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Lịch Đóng Cửa / Bảo Trì: {{ $court->name }}</h3>
                </div>
                <a href="{{ route('owner.courts.closures.create', $court) }}" class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    + Thêm Lịch Khóa
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                            <th class="p-5 font-semibold text-sm">Ngày Khóa</th>
                            <th class="p-5 font-semibold text-sm">Khung Giờ</th>
                            <th class="p-5 font-semibold text-sm">Lý Do</th>
                            <th class="p-5 font-semibold text-sm text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @forelse($closures as $closure)
                        <tr class="hover:bg-pink-50/30 transition-colors">
                            <td class="p-5 font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($closure->date)->format('d/m/Y') }}
                            </td>
                            <td class="p-5 text-gray-700">
                                @if($closure->start_time && $closure->end_time)
                                    <span class="bg-pink-100 text-pink-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ \Carbon\Carbon::parse($closure->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($closure->end_time)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Khóa cả ngày</span>
                                @endif
                            </td>
                            <td class="p-5 text-gray-600 italic">{{ $closure->reason }}</td>
                            <td class="p-5 text-right">
                                <form action="{{ route('owner.closures.destroy', $closure) }}" method="POST" class="inline-block" onsubmit="return confirm('Mở lại lịch cho ngày này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-gray-400 hover:text-green-500">Mở lại sân</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-gray-400">Sân này hiện không có lịch khóa đột xuất nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $closures->links() }}
            </div>
        </div>
    </div>
</x-app-layout>