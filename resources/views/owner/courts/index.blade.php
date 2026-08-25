<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Danh sách Sân Con') }} — {{ $venue->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 bg-white border-l-4 border-pink-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm flex justify-between items-center transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <div>
                    <a href="{{ route('owner.venues.index') }}" class="text-sm font-bold text-pink-500 hover:text-pink-700 mb-2 inline-block transition">
                        &larr; Quay lại danh sách Khu sân
                    </a>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Sân con thuộc: <span class="text-pink-600">{{ $venue->name }}</span></h3>
                </div>
                <a href="{{ route('owner.venues.courts.create', $venue) }}" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    + Thêm Sân Con Mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                                <th class="p-5 font-semibold text-sm">Tên Sân Con</th>
                                <th class="p-5 font-semibold text-sm">Môn Thể Thao</th>
                                <th class="p-5 font-semibold text-sm">Loại Mặt Sân</th>
                                <th class="p-5 font-semibold text-sm">Số Người Tối Đa</th>
                                <th class="p-5 font-semibold text-sm text-center">Trạng Thái</th>
                                <th class="p-5 font-semibold text-sm text-right">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50">
                            @forelse($courts as $court)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="font-bold text-gray-800 text-base">{{ $court->name }}</div>
                                    <div class="text-xs text-gray-400 mt-1 font-mono">ID: #{{ $court->id }}</div>
                                </td>
                                <td class="p-5 font-medium text-gray-700">{{ $court->sport->name ?? 'N/A' }}</td>
                                <td class="p-5 text-gray-600 capitalize">
                                    {{ str_replace('_', ' ', $court->surface_type) }}
                                </td>
                                <td class="p-5 text-gray-600">
                                    {{ $court->max_players ? $court->max_players . ' người' : 'Không giới hạn' }}
                                </td>
                                <td class="p-5 text-center">
                                    @if($court->status === 'active')
                                        <span class="inline-flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Hoạt động
                                        </span>
                                    @elseif($court->status === 'maintenance')
                                        <span class="inline-flex items-center bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">
                                            <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Bảo trì
                                        </span>
                                    @else
                                        <span class="inline-flex items-center bg-gray-50 text-gray-700 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                                            <span class="w-2 h-2 rounded-full bg-gray-500 mr-2"></span>Đóng cửa
                                        </span>
                                    @endif
                                </td>
                                <td class="p-5 text-right space-x-2 text-sm font-medium">
                                    <!-- Nút cấu hình Giá & Giờ -->
                                    <a href="{{ route('owner.courts.slots.index', $court) }}" class="inline-block text-indigo-500 hover:text-indigo-700 transition font-bold border border-indigo-200 bg-indigo-50 px-3 py-1.5 rounded-lg">Giá & Giờ</a>
                                    
                                    <!-- Nút Khóa lịch (Mới thêm) -->
                                    <a href="{{ route('owner.courts.closures.index', $court) }}" class="inline-block text-red-500 hover:text-red-700 transition font-bold border border-red-200 bg-red-50 px-3 py-1.5 rounded-lg">Khóa lịch</a>

                                    <a href="{{ route('owner.courts.edit', $court) }}" class="inline-block text-pink-500 hover:text-pink-700 transition px-2">Sửa</a>
                                    
                                    <form action="{{ route('owner.courts.destroy', $court) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân con này? Mọi khung giờ và dữ liệu liên quan sẽ bị xóa.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition px-2">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-500">
                                    <div class="mb-4 text-pink-200 flex justify-center">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700">Chưa có sân con nào</p>
                                    <p class="text-sm mt-1">Khu sân này chưa được thiết lập sân con. Bấm "Thêm Sân Con Mới" để bắt đầu.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $courts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>     