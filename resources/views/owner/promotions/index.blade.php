<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Quản lý Khuyến Mãi — {{ $venue->name }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 bg-white border-l-4 border-pink-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm flex justify-between items-center">
                    <span class="font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <div>
                    <a href="{{ route('owner.venues.index') }}" class="text-sm font-bold text-pink-500 hover:text-pink-700 mb-2 inline-block">
                        &larr; Quay lại danh sách Khu sân
                    </a>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Mã Giảm Giá</h3>
                </div>
                <a href="{{ route('owner.venues.promotions.create', $venue) }}" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    + Tạo Mã Mới
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-pink-100 overflow-hidden">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-pink-50/70 border-b border-pink-100 text-pink-900">
                            <th class="p-5 font-semibold text-sm">Mã Code</th>
                            <th class="p-5 font-semibold text-sm">Mức Giảm</th>
                            <th class="p-5 font-semibold text-sm">Đã Dùng / Tối Đa</th>
                            <th class="p-5 font-semibold text-sm">Hạn Sử Dụng</th>
                            <th class="p-5 font-semibold text-sm text-center">Trạng Thái</th>
                            <th class="p-5 font-semibold text-sm text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @forelse($promotions as $promo)
                        <tr class="hover:bg-pink-50/30 transition-colors">
                            <td class="p-5 font-bold text-pink-600 text-lg">{{ $promo->code }}</td>
                            <td class="p-5 font-medium text-gray-800">
                                {{ $promo->discount_type === 'percent' ? number_format($promo->discount_value, 0) . '%' : number_format($promo->discount_value, 0, ',', '.') . ' đ' }}
                            </td>
                            <td class="p-5 text-gray-600">
                                <span class="font-bold text-gray-800">{{ $promo->used_count }}</span> / {{ $promo->max_uses ?? '∞' }}
                            </td>
                            <td class="p-5 text-gray-600 text-sm">
                                {{ \Carbon\Carbon::parse($promo->starts_at)->format('d/m/Y') }}<br>
                                <span class="text-xs text-gray-400">đến</span> {{ \Carbon\Carbon::parse($promo->expires_at)->format('d/m/Y') }}
                            </td>
                            <td class="p-5 text-center">
                                @if($promo->isValid())
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Đang chạy</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">Vô hiệu</span>
                                @endif
                            </td>
                            <td class="p-5 text-right space-x-4">
                                <!-- Nút Sửa -->
                                <a href="{{ route('owner.promotions.edit', $promo) }}" class="text-pink-500 hover:text-pink-700 font-medium transition-colors">
                                    Sửa
                                </a>

                                <!-- Nút Xóa -->
                                <form action="{{ route('owner.promotions.destroy', $promo) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa mã giảm giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 font-medium transition-colors">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-gray-500">Khu sân này chưa có mã khuyến mãi nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>