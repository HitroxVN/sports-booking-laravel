@extends('layouts.customer')

@section('title', 'Chính sách thanh toán — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('help') }}" class="hover:text-primary-600 transition-colors">Hỗ trợ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Chính sách thanh toán</span>
        </nav>

        {{-- Hero Header --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-8 sm:p-12 mb-10 shadow-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Thanh toán an toàn tiêu chuẩn quốc tế
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight mb-3">
                Chính Sách Thanh Toán & Bảo Mật Giao Dịch
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base max-w-3xl leading-relaxed">
                Tất cả các giao dịch thanh toán đặt sân thể thao trên hệ thống Arena đều được bảo mật nhiều lớp, cam kết xử lý tức thì và tự động xác nhận đơn trong vòng 60 giây.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <x-policy-sidebar active="payment" />

            {{-- Nội dung chi tiết --}}
            <main class="lg:col-span-3 space-y-8 text-zinc-800 dark:text-zinc-200">
                {{-- Các phương thức hỗ trợ --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        1. Các phương thức thanh toán được hỗ trợ
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40">
                            <div class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">QR Ngân hàng</span>
                                VNPAY-QR & VietQR
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Quét mã QR tiện lợi qua ứng dụng của hơn 40 ngân hàng nội địa (Vietcombank, MB, Techcombank, BIDV, Agribank, ACB,...). Tiền khớp tức thì.
                            </p>
                        </div>
                        <div class="p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40">
                            <div class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Thẻ Quốc Tế</span>
                                Visa, Mastercard, JCB
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Hỗ trợ thẻ tín dụng và thẻ ghi nợ quốc tế, xác thực 3D-Secure mã OTP ngân hàng bảo vệ an toàn tối đa cho chủ thẻ.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quy trình giữ chỗ & thanh toán --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        2. Quy trình giữ chỗ 15 phút
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
                        Nhằm đảm bảo tính công bằng và tránh tình trạng nhiều người cùng đặt 1 khung giờ:
                    </p>
                    <ul class="space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Khi bấm đặt sân, hệ thống sẽ <strong>khóa giữ chỗ khung giờ đó trong vòng 15 phút</strong>. Trong thời gian này, không khách hàng nào khác có thể đặt khung giờ này.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Nếu sau 15 phút thanh toán chưa hoàn tất, hệ thống sẽ tự động hủy đơn giữ chỗ và mở lại khung giờ cho những người chơi khác.</span>
                        </li>
                    </ul>
                </div>

                {{-- Cam kết bảo mật --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                        3. Cam kết an toàn & Xử lý sự cố giao dịch
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4">
                        Arena không lưu trữ bất kỳ thông tin số thẻ hay mật khẩu ngân hàng của quý khách trên máy chủ. Toàn bộ quá trình thanh toán diễn ra trực tiếp trên cổng thanh toán đạt chứng chỉ bảo mật quốc tế <strong>PCI DSS Cấp 1</strong>.
                    </p>
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
                        <strong>Lưu ý sự cố:</strong> Trường hợp tài khoản ngân hàng của bạn đã bị trừ tiền nhưng website chưa kịp cập nhật trạng thái đơn (thường do lỗi mạng hoặc gián đoạn phía cổng ngân hàng), vui lòng giữ lại biên lai chuyển khoản và liên hệ ngay Hotline <strong>{{ setting('contact_hotline', '1900 1234') }}</strong>. Chúng tôi sẽ đối soát và kích hoạt đơn đặt sân cho bạn trong vòng 10 phút.
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
