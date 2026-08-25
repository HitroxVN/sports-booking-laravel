<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Chi Tiết Đơn Đặt Sân: <span class="text-pink-600">#{{ $booking->code }}</span>
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-white border-l-4 border-green-500 text-gray-700 px-6 py-4 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('owner.bookings.index') }}" class="inline-flex items-center text-pink-500 hover:text-pink-700 text-sm font-bold">
                    &larr; Quay lại danh sách
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Cột trái: Thông tin chi tiết -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Khối thông tin khách & sân -->
                    <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                        <h3 class="text-lg font-extrabold text-gray-800 border-b border-gray-100 pb-4 mb-6">Thông Tin Đơn Đặt</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Khách Hàng</h4>
                                <p class="font-medium text-gray-800 mb-1">{{ $booking->user->name ?? 'Khách Vãng Lai' }}</p>
                                <p class="text-gray-600 mb-1">📞 {{ $booking->user->phone ?? 'Không có SĐT' }}</p>
                                <p class="text-gray-600">✉️ {{ $booking->user->email ?? 'Không có Email' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Sân Thể Thao</h4>
                                <p class="font-medium text-gray-800 mb-1">{{ $booking->court->venue->name }}</p>
                                <p class="text-gray-600 mb-1">Sân: <span class="font-bold">{{ $booking->court->name }}</span></p>
                                <p class="text-gray-600">Loại: {{ str_replace('_', ' ', $booking->court->surface_type) }}</p>
                            </div>
                        </div>

                        <div class="mt-8 bg-pink-50 p-6 rounded-2xl">
                            <h4 class="text-sm font-bold text-pink-500 uppercase tracking-wider mb-4">Thời Gian & Giá Trị</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Ngày đá</p>
                                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Khung giờ</p>
                                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Đã cọc</p>
                                    <p class="font-bold text-green-600">{{ number_format($booking->deposit_amount, 0, ',', '.') }} đ</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Tổng tiền</p>
                                    <p class="font-bold text-pink-600 text-lg">{{ number_format($booking->total_amount, 0, ',', '.') }} đ</p>
                                </div>
                            </div>
                        </div>

                        @if($booking->notes)
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-gray-700">Ghi chú của khách:</p>
                            <p class="text-gray-600 italic bg-gray-50 p-4 rounded-xl mt-2 border border-gray-100">{{ $booking->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Khối lịch sử thanh toán -->
                    <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8">
                        <h3 class="text-lg font-extrabold text-gray-800 border-b border-gray-100 pb-4 mb-6">Lịch Sử Thanh Toán</h3>
                        @if($booking->payments->count() > 0)
                            <div class="space-y-4">
                                @foreach($booking->payments as $payment)
                                    <div class="flex justify-between items-center p-4 border border-gray-100 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ number_format($payment->amount, 0, ',', '.') }} đ <span class="text-xs font-normal text-gray-500 ml-2 uppercase">{{ $payment->gateway }}</span></p>
                                            <p class="text-xs text-gray-500 mt-1">Mã GD: {{ $payment->gateway_txn_id ?? 'N/A' }} | Cập nhật lúc: {{ $payment->updated_at->format('H:i d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            @if($payment->status === 'success')
                                                <span class="text-green-600 font-bold text-sm">Thành công</span>
                                            @else
                                                <span class="text-yellow-600 font-bold text-sm">{{ $payment->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-4">Chưa có giao dịch thanh toán nào được ghi nhận.</p>
                        @endif
                    </div>
                </div>

                <!-- Cột phải: Form cập nhật trạng thái -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8 sticky top-8">
                        <h3 class="text-lg font-extrabold text-gray-800 border-b border-gray-100 pb-4 mb-6">Thao Tác Đơn</h3>
                        
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-gray-500 mb-2">Trạng thái hiện tại:</p>
                            @if($booking->isPending())
                                <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-xl text-sm font-bold block text-center">Chờ xử lý</span>
                            @elseif($booking->isConfirmed())
                                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-bold block text-center">Đã xác nhận</span>
                            @elseif($booking->isCompleted())
                                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-xl text-sm font-bold block text-center">Hoàn thành</span>
                            @elseif($booking->isCancelled())
                                <span class="bg-red-100 text-red-700 px-4 py-2 rounded-xl text-sm font-bold block text-center">Đã hủy</span>
                                <p class="text-xs text-red-500 mt-2 italic text-center">Lý do: {{ $booking->cancel_reason }}</p>
                            @endif
                        </div>

                        <form action="{{ route('owner.bookings.update', $booking) }}" method="POST" x-data="{ status: '{{ $booking->status }}' }">
                            @csrf
                            @method('PUT')
                            
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Đổi trạng thái mới</label>
                            <select name="status" x-model="status" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm mb-4">
                                <option value="pending">Chờ xử lý</option>
                                <option value="confirmed">Xác nhận đơn</option>
                                <option value="completed">Đã đá / Hoàn thành</option>
                                <option value="cancelled">Hủy đơn</option>
                            </select>

                            <!-- Khung nhập lý do hủy chỉ hiện khi chọn Hủy đơn -->
                            <div x-show="status === 'cancelled'" class="mb-4" x-transition>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lý do hủy (Bắt buộc) <span class="text-red-500">*</span></label>
                                <input type="text" name="cancel_reason" placeholder="VD: Sân ngập nước, Khách gọi hủy..." x-bind:required="status === 'cancelled'" class="w-full border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 shadow-sm">
                            </div>

                            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl shadow-md hover:bg-black transition-all">
                                Lưu Trạng Thái
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>