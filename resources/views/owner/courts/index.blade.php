<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Quản lý Sân Con') }} — {{ $venue->name }}
            </h2>
            <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-sm hover:bg-pink-50 hover:text-pink-600 transition">
                &larr; Trở về Khu Sân
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative shadow-sm" role="alert">
                    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Danh Sách Sân Con</h3>
                    <p class="text-sm text-gray-500">Quản lý các sân thể thao thuộc khu vực này</p>
                </div>
                <a href="{{ route('owner.venues.courts.create', $venue) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                    + Thêm sân con mới
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-50/50 text-gray-600 text-sm font-bold uppercase tracking-wider border-b border-pink-100">
                                <th class="p-4">Tên Sân Con</th>
                                <th class="p-4">Môn Thể Thao</th>
                                <th class="p-4 text-center">Loại Mặt Sân</th>
                                <th class="p-4 text-center">Người Tối Đa</th>
                                <th class="p-4 text-center">Trạng Thái</th>
                                <th class="p-4 text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50 text-gray-700">
                            @forelse($courts as $court)
                                <tr class="hover:bg-pink-50/30 transition-colors">
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $court->name }}
                                    </td>
                                    <td class="p-4 text-sm font-medium">
                                        {{ $court->sport->name ?? 'N/A' }}
                                    </td>
                                    <td class="p-4 text-sm text-center">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold border border-gray-200">
                                            {{ $court->surface_type_name }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-center font-medium">
                                        {{ $court->max_players ? $court->max_players . ' người' : '--' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($court->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                {{ $court->status_name }}
                                            </span>
                                        @elseif($court->status === 'maintenance')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                {{ $court->status_name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                {{ $court->status_name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 flex items-center justify-center space-x-4">
                                        <a href="{{ route('owner.courts.slots.index', $court) }}" class="text-blue-500 hover:text-blue-700 text-sm font-bold transition-colors">
                                            Khung giờ & Giá
                                        </a>
                                        <a href="{{ route('owner.courts.edit', $court) }}" class="text-pink-500 hover:text-pink-700 text-sm font-bold transition-colors">
                                            Sửa
                                        </a>
                                        <form action="{{ route('owner.courts.destroy', $court) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này không? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 text-sm font-bold transition-colors">
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-pink-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <p class="font-medium">Khu sân này chưa có sân con nào.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($courts->hasPages())
                    <div class="p-4 border-t border-pink-100 bg-gray-50">
                        {{ $courts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>