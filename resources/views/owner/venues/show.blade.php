<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Chi Tiết Khu Sân') }} — {{ $venue->name }}
            </h2>
            <a href="{{ route('owner.venues.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-sm hover:bg-pink-50 hover:text-pink-600 transition">
                &larr; Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                
                <!-- Khung hiển thị ảnh đại diện của Khu Sân từ bảng venue_images -->
                <div class="mb-8 relative overflow-hidden rounded-2xl shadow-sm border border-pink-100 bg-gray-100" style="height: 280px; width: 100%;">
                    @if($venue->images && $venue->images->count() > 0)
                    @php
                        $img = $venue->images->first();
                    @endphp
                    <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $venue->name }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    @else
                    <div class="flex flex-col items-center justify-center h-full text-gray-400" style="height: 280px;">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Chưa có hình ảnh khu sân</span>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-4">{{ $venue->name }}</h3>
                        
                        <div class="space-y-3 text-sm text-gray-600">
                            <p><strong>Địa chỉ:</strong> {{ $venue->address }} {{ $venue->district ? '— ' . $venue->district : '' }} {{ $venue->city ? '(' . $venue->city . ')' : '' }}</p>
                            <p><strong>Điện thoại:</strong> {{ $venue->phone ?? 'Chưa cập nhật' }}</p>
                            <p><strong>Email:</strong> {{ $venue->email ?? 'Chưa cập nhật' }}</p>
                            <p class="flex items-center">
                                <strong class="mr-2">Trạng thái:</strong> 
                                @php
                                    $statusClasses = match($venue->status) {
                                        'active' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-red-100 text-red-800',
                                    };
                                    $statusLabels = [
                                        'active' => 'Hoạt động',
                                        'pending' => 'Chờ duyệt',
                                        'closed' => 'Đóng cửa'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                    {{ $statusLabels[$venue->status] ?? $venue->status }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Mô tả khu sân</h4>
                        <div class="text-sm text-gray-600 bg-pink-50/50 p-5 rounded-2xl border border-pink-100 min-h-[120px] leading-relaxed">
                            {{ $venue->description ?? 'Không có mô tả chi tiết cho khu sân này.' }}
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-4">
                    <a href="{{ route('owner.venues.edit', $venue) }}" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-bold rounded-xl text-sm shadow transition">
                        ✏️ Chỉnh sửa thông tin
                    </a>
                    <a href="{{ route('owner.venues.courts.index', $venue) }}" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl text-sm shadow transition">
                        🏟 Quản lý Sân con bên trong
                    </a>
                    <a href="{{ route('owner.venues.promotions.index', $venue) }}" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-sm shadow transition">
                        🎟 Quản lý Khuyến mãi
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>