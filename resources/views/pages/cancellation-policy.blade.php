@extends('layouts.customer')

@section('title', 'Chính sách hủy sân & Hoàn tiền — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('help') }}" class="hover:text-primary-600 transition-colors">Hỗ trợ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Chính sách hủy sân</span>
        </nav>

        {{-- Hero Header --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-8 sm:p-12 mb-10 shadow-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Minh bạch & Đảm bảo quyền lợi hai bên
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight mb-3">
                Chính Sách Hủy Sân & Hoàn Tiền
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base max-w-3xl leading-relaxed">
                Arena luôn tôn trọng kế hoạch của người chơi đồng thời chia sẻ rủi ro chi phí vận hành với các chủ sân. Dưới đây là các quy định chi tiết về việc thay đổi lịch và hủy đơn đặt sân.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <x-policy-sidebar active="cancellation" />

            {{-- Nội dung chi tiết --}}
            <main class="lg:col-span-3 space-y-8 text-zinc-800 dark:text-zinc-200">
                {{-- Bảng tóm tắt khung giờ hủy --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        1. Khung thời gian và tỷ lệ hoàn tiền
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                        Tỷ lệ hoàn tiền được tính dựa trên mốc thời gian khách hàng thực hiện yêu cầu hủy đơn so với thời điểm khung giờ bắt đầu thi đấu:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Trước >= 6 tiếng</span>
                            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-300 mt-2 mb-1">Hoàn 100%</div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Miễn phí hủy hoàn toàn nếu báo trước từ 6 tiếng trở lên.
                            </p>
                        </div>
                        <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Từ 2 đến 6 tiếng</span>
                            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-300 mt-2 mb-1">Hoàn 50%</div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Khách hàng nhận 50% giá trị; 50% còn lại bù đắp cho chủ sân.
                            </p>
                        </div>
                        <div class="p-5 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/60">
                            <span class="text-xs font-bold uppercase tracking-wider text-red-700 dark:text-red-400">Dưới 2 tiếng</span>
                            <div class="text-2xl font-extrabold text-red-600 dark:text-red-300 mt-2 mb-1">Không hoàn tiền</div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Không thể hoàn do khung giờ cận kề, không tìm kịp người thay thế.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Trường hợp bất khả kháng --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        2. Quy định trong trường hợp bất khả kháng
                    </h2>
                    <ul class="space-y-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><strong>Thời tiết cực đoan (Mưa bão lớn, giông lốc):</strong> Áp dụng cho các cụm sân ngoài trời không có mái che. Khách hàng và chủ sân có thể chủ động liên hệ dời lịch sang buổi khác hoặc yêu cầu Arena hỗ trợ hoàn 100% tiền cọc.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><strong>Sự cố kỹ thuật từ cơ sở sân:</strong> Trường hợp mất điện kéo dài, bảo dưỡng hệ thống mặt sân đột xuất ngoài ý muốn, chủ sân sẽ chủ động hủy và người đặt sân được hoàn 100% tiền ngay lập tức kèm voucher bồi thường.</span>
                        </li>
                    </ul>
                </div>

                {{-- Các bước thao tác hủy sân --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        3. Hướng dẫn các bước hủy đơn trên ứng dụng
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-600 text-white font-bold text-sm">1</span>
                            <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Chọn đơn đặt</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Vào mục <strong>"Đơn đặt sân của tôi"</strong> và bấm xem chi tiết đơn bạn muốn hủy.</p>
                        </div>
                        <div class="space-y-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-600 text-white font-bold text-sm">2</span>
                            <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Gửi yêu cầu hủy</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Chọn nút <strong>"Hủy đơn"</strong>, ghi rõ lý do. Hệ thống sẽ tự động tính số tiền được hoàn.</p>
                        </div>
                        <div class="space-y-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-600 text-white font-bold text-sm">3</span>
                            <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Nhận hoàn tiền</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Số tiền hoàn sẽ về tài khoản thanh toán ban đầu của bạn trong vòng từ <strong>1 đến 3 ngày làm việc</strong>.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
