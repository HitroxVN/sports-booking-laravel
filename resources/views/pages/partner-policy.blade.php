@extends('layouts.customer')

@section('title', 'Chính sách đối tác chủ sân — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('help') }}" class="hover:text-primary-600 transition-colors">Hỗ trợ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Chính sách đối tác chủ sân</span>
        </nav>

        {{-- Hero Header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 via-emerald-700 to-primary-900 text-white p-8 sm:p-12 mb-10 shadow-lc-lg">
            <div class="relative z-10 max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-emerald-100 backdrop-blur-md mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Hợp tác kinh doanh bền vững
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">
                    Chính Sách Đối Tác & Quy Chế Hợp Tác Chủ Sân
                </h1>
                <p class="text-white/85 text-sm sm:text-base leading-relaxed">
                    Đồng hành cùng Arena để số hóa toàn diện quy trình vận hành cụm sân, tối đa hóa công suất khung giờ trống và tiếp cận hàng chục ngàn người chơi thể thao sôi động trên toàn quốc.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-cta-400 hover:bg-cta-500 text-zinc-900 font-bold text-sm rounded-xl shadow-lc transition-all">
                        Đăng ký trở thành đối tác
                    </a>
                    <a href="tel:{{ str_replace(' ', '', setting('contact_hotline', '1900 1234')) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-medium text-sm rounded-xl backdrop-blur-md transition-all">
                        Hotline tư vấn: {{ setting('contact_hotline', '1900 1234') }}
                    </a>
                </div>
            </div>
            {{-- Decorative circles --}}
            <div class="absolute -right-12 -bottom-12 w-72 h-72 bg-cta-400/15 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <x-policy-sidebar active="partner" />

            {{-- Nội dung chi tiết chính sách đối tác --}}
            <main class="lg:col-span-3 space-y-8 text-zinc-800 dark:text-zinc-200">
                {{-- 1. Quyền lợi đối tác --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
                        1. Quyền lợi vượt trội khi hợp tác với Arena
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1.5">Lấp đầy công suất giờ thấp điểm</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Tiếp cận mạng lưới hàng chục ngàn người chơi đang tìm kiếm sân hàng ngày, giúp tăng trưởng doanh thu 30% - 50% ở các khung giờ vắng.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1.5">Phần mềm quản trị sân miễn phí</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Trang Kênh chủ sân hiện đại: quản lý lịch biểu trực quan, khóa sân khi bảo trì, nhận thông báo đặt sân mới ngay tức khắc qua hệ thống.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1.5">Báo cáo & Quyết toán minh bạch</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Số liệu doanh thu, lượt khách, tỷ lệ lấp đầy theo ngày/tuần/tháng được thống kê tự động kèm tính năng xuất báo cáo CSV chi tiết.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                            <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1.5">Hỗ trợ truyền thông & Quảng bá</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Khu sân của bạn sẽ được gắn nhãn Xác minh, xuất hiện nổi bật tại trang tìm kiếm và được Arena quảng bá trên các chuyên mục tin tức thể thao.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. Tiêu chuẩn & Điều kiện gia nhập --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
                        2. Tiêu chuẩn gia nhập đối tác Arena
                    </h2>
                    <ul class="space-y-3.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">✓</span>
                            <span><strong>Cơ sở vật chất:</strong> Mặt sân, hệ thống chiếu sáng, lưới, khung thành đạt tiêu chuẩn an toàn cho từng bộ môn thi đấu; có khu vực nghỉ ngơi và bãi gửi xe cho khách hàng.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">✓</span>
                            <span><strong>Cam kết giá minh bạch:</strong> Giá niêm yết trên Arena phải thống nhất với giá bán trực tiếp tại quầy của cơ sở sân, không tự ý thu thêm phụ phí ngoài quy định.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">✓</span>
                            <span><strong>Đảm bảo lịch đặt:</strong> Giữ đúng sân và khung giờ cho khách hàng đã thanh toán qua ứng dụng. Trường hợp bất khả kháng phải chủ động liên hệ dời lịch hoặc hỗ trợ trước giờ thi đấu.</span>
                        </li>
                    </ul>
                </div>

                {{-- 3. Biểu phí & Chu kỳ quyết toán --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
                        3. Biểu phí dịch vụ & Chính sách quyết toán doanh thu
                    </h2>
                    <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        <p>
                            Arena hoạt động trên mô hình <strong>hợp tác chia sẻ doanh thu thực tế (Revenue Sharing)</strong>:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30">
                                <span class="text-xs font-bold uppercase text-zinc-400">Phí khởi tạo & duy trì</span>
                                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 mb-1">0 VNĐ (Hoàn toàn miễn phí)</div>
                                <p class="text-xs text-zinc-500">Chủ sân không phải đóng bất kỳ khoản phí thành viên hay phí duy trì định kỳ hàng tháng nào.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30">
                                <span class="text-xs font-bold uppercase text-zinc-400">Chu kỳ thanh toán</span>
                                <div class="text-xl font-bold text-primary-600 dark:text-primary-400 mt-1 mb-1">Hàng tuần / Hàng tháng</div>
                                <p class="text-xs text-zinc-500">Tiền đặt sân được chuyển khoản trực tiếp vào số tài khoản ngân hàng của chủ sân theo đúng kỳ đối soát.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Quy trình 4 bước gia nhập --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                        <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
                        4. Quy trình 4 bước đơn giản để bắt đầu
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800 relative">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs mb-3">1</span>
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1">Đăng ký tài khoản</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Tạo tài khoản với vai trò Chủ sân trên Arena chỉ mất 30 giây.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800 relative">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs mb-3">2</span>
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1">Thiết lập khu sân</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Nhập thông tin sân, tải ảnh thực tế và cài đặt bảng giá khung giờ.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800 relative">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs mb-3">3</span>
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1">Kiểm định & Duyệt</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Arena liên hệ xác nhận và phê duyệt hiển thị trong vòng 24 giờ.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800 relative">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs mb-3">4</span>
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-1">Bắt đầu đón khách</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Khu sân xuất hiện trên bản đồ tìm kiếm và sẵn sàng nhận đơn đặt online.</p>
                        </div>
                    </div>
                </div>

                {{-- Banner Call to Action --}}
                <div class="rounded-3xl bg-gradient-to-r from-primary-800 to-emerald-900 text-white p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lc">
                    <div class="space-y-1 text-center sm:text-left">
                        <h3 class="text-xl font-bold">Sẵn sàng phát triển cùng Arena?</h3>
                        <p class="text-sm text-white/80">Tham gia ngay cùng mạng lưới hơn 200 cụm sân hàng đầu trên cả nước.</p>
                    </div>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-cta-400 hover:bg-cta-500 text-zinc-900 font-bold text-sm rounded-xl shadow-lg shrink-0 transition-transform active:scale-95">
                        Đăng ký làm chủ sân ngay
                    </a>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
