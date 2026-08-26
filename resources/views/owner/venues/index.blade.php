<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Quản lý Khu Sân') }}
        </h2>
        <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-sm hover:bg-pink-50 hover:text-pink-600 transition">
            &larr; Trở về trang chủ
        </a>
    </div>
</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Thông báo thành công -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 bg-white border-l-4 border-pink-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm flex justify-between items-center transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
            @endif

            <!-- Header Action -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Khu Sân Của Tôi</h3>
                    <p class="text-gray-500 text-sm mt-1">Quản lý tất cả cơ sở và sân thể thao của bạn tại đây</p>
                </div>
                <a href="{{ route('owner.venues.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Thêm khu sân
                </a>
            </div>

            <!-- Bảng dữ liệu -->
            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                                <th class="p-5 font-semibold text-sm">Tên Sân & Mã</th>
                                <th class="p-5 font-semibold text-sm">Vị trí</th>
                                <th class="p-5 font-semibold text-sm">Liên hệ</th>
                                <th class="p-5 font-semibold text-sm text-center">Trạng thái</th>
                                <th class="p-5 font-semibold text-sm text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-50">
                            @forelse($venues as $venue)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="font-bold text-gray-800 text-base">{{ $venue->name }}</div>
                                    <div class="text-xs text-gray-400 mt-1 font-mono">ID: #{{ $venue->id }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="text-gray-700 text-sm truncate max-w-xs">{{ $venue->address }}</div>
                                    <div class="text-gray-500 text-xs mt-1">{{ $venue->district }}, {{ $venue->city }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="text-gray-700 text-sm font-medium">{{ $venue->phone }}</div>
                                    <div class="text-gray-500 text-xs mt-1">{{ $venue->email ?? 'N/A' }}</div>
                                </td>
                                <td class="p-5 text-center">
                                    @if($venue->status === 'active')
                                        <span class="inline-flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Hoạt động
                                        </span>
                                    @elseif($venue->status === 'pending')
                                        <span class="inline-flex items-center bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">
                                            <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Chờ duyệt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center bg-gray-50 text-gray-700 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                                            <span class="w-2 h-2 rounded-full bg-gray-500 mr-2"></span>Đóng cửa
                                        </span>
                                    @endif
                                </td>
                                <td class="p-5 text-right space-x-2 text-sm font-medium">
                                    <!-- Nút quản lý Sân Con -->
                                    <a href="{{ route('owner.venues.courts.index', $venue) }}" class="inline-block text-indigo-500 hover:text-indigo-700 transition font-bold border border-indigo-200 bg-indigo-50 px-3 py-1.5 rounded-lg">Sân con ({{ $venue->courts()->count() }})</a>

                                    <!-- Nút quản lý Khuyến Mãi -->
                                    <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="inline-block text-yellow-600 hover:text-yellow-800 transition font-bold border border-yellow-200 bg-yellow-50 px-3 py-1.5 rounded-lg">Khuyến mãi</a>

                                    <!-- Nút Xem Chi Tiết Vừa Được Bổ Sung -->
                                    <a href="{{ route('owner.venues.show', $venue) }}" class="inline-block text-blue-500 hover:text-blue-700 transition px-2">Chi tiết</a>

                                    <a href="{{ route('owner.venues.edit', $venue) }}" class="inline-block text-pink-500 hover:text-pink-700 transition px-2">Sửa</a>
                                    
                                    <form action="{{ route('owner.venues.destroy', $venue) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition px-2">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-16 text-center text-gray-500">
                                    <div class="mb-4 text-pink-200 flex justify-center">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700">Bạn chưa có khu sân nào</p>
                                    <p class="text-sm mt-1">Hãy bấm "Thêm khu sân" để bắt đầu thiết lập cơ sở của bạn.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $venues->links() }}
            </div>
        </div>
    </div>
</x-app-layout>