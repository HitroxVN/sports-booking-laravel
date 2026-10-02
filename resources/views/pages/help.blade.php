@extends('layouts.customer')

@section('title', 'Trung tâm trợ giúp — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Trung tâm trợ giúp</span>
        </nav>

        {{-- Hero Banner --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white p-8 sm:p-12 mb-12 shadow-lc-lg">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-cta-300 backdrop-blur-md mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hỗ trợ khách hàng 24/7
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">
                    Chúng tôi có thể giúp gì cho bạn?
                </h1>
                <p class="text-white/80 text-sm sm:text-base leading-relaxed">
                    Tra cứu câu hỏi thường gặp, hướng dẫn đặt sân, quy trình thanh toán và chính sách hoàn tiền tại Arena Sports.
                </p>
            </div>
            {{-- Decorative circles --}}
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-cta-400/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-20 -top-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar điều hướng trang chính sách & hỗ trợ --}}
            <x-policy-sidebar active="help" />

            {{-- Nội dung FAQ chính --}}
            <main class="lg:col-span-3 space-y-8" x-data="{ openFaq: 1 }">
                {{-- Nhóm 1: Dành cho Khách hàng đặt sân --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">1. Đặt sân & Trải nghiệm</h2>
                            <p class="text-xs text-zinc-500">Hướng dẫn các bước thao tác cho người chơi thể thao</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        {{-- FAQ 1 --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 1 ? null : 1)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Làm thế nào để tìm và đặt sân trên Arena?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 1}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 1" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Bạn có thể sử dụng thanh tìm kiếm ở đầu trang chủ hoặc vào mục <strong>"Tìm sân"</strong> để lọc theo môn thể thao (bóng đá, cầu lông, pickleball, tennis), khu vực quận/huyện, khoảng giá và khung giờ mong muốn. Sau khi chọn khu sân ưng ý, chọn sân con và khung giờ trống rồi bấm <strong>"Đặt sân ngay"</strong> để tiến hành thanh toán giữ chỗ.
                            </div>
                        </div>

                        {{-- FAQ 2 --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 2 ? null : 2)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Tôi có thể đặt sân trước tối đa bao nhiêu ngày?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 2}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 2" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Thông thường các khu sân trên Arena mở lịch đặt trước từ <strong>7 đến 30 ngày</strong> tùy thuộc vào chính sách của từng chủ sân. Bạn có thể xem lịch biểu chi tiết tại trang của từng khu sân.
                            </div>
                        </div>

                        {{-- FAQ 3 --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 3 ? null : 3)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Đến sân thì cần xuất trình thông tin gì để nhận sân?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 3}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 3" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Bạn chỉ cần vào mục <strong>"Đơn đặt sân của tôi"</strong> trên website Arena và đưa mã đơn đặt sân hoặc số điện thoại đăng ký cho nhân viên quản lý sân kiểm tra và nhận sân ngay lập tức.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nhóm 2: Thanh toán & Ưu đãi --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">2. Thanh toán & Ưu đãi thành viên</h2>
                            <p class="text-xs text-zinc-500">Các phương thức thanh toán an toàn, tích điểm đổi voucher</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 4 ? null : 4)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Arena hỗ trợ những phương thức thanh toán nào?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 4}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 4" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Chúng tôi hỗ trợ thanh toán qua cổng <strong>VNPAY</strong> (quét mã QR ngân hàng, thẻ ATM nội địa, thẻ quốc tế Visa/Mastercard) và chuyển khoản ngân hàng tự động. Mọi giao dịch đều được mã hóa bảo mật chuẩn ngân hàng.
                            </div>
                        </div>

                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 5 ? null : 5)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Làm sao để tích điểm thành viên và sử dụng voucher?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 5}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 5" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Mỗi lần hoàn thành đơn đặt sân, tài khoản của bạn sẽ tự động được cộng điểm thưởng tích lũy (Loyalty points). Bạn có thể vào trang <strong>Hồ sơ cá nhân</strong> để đổi số điểm này thành các voucher giảm giá trực tiếp cho lần đặt sân tiếp theo.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nhóm 3: Dành cho Chủ sân đối tác --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">3. Dành cho Chủ sân (Đối tác)</h2>
                            <p class="text-xs text-zinc-500">Đăng ký kinh doanh, quản trị công suất sân và thanh toán doanh thu</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 6 ? null : 6)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Làm sao để đăng ký đưa khu sân của tôi lên Arena?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 6}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 6" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Bạn đăng ký tài khoản với vai trò <strong>"Chủ sân"</strong>. Sau khi đăng nhập, hệ thống sẽ mở Kênh chủ sân với giao diện tạo mới khu sân, thêm hình ảnh, sân con và bảng giá theo từng khung giờ. Đội ngũ kiểm duyệt của Arena sẽ phê duyệt khu sân trong vòng 24 giờ làm việc.
                            </div>
                        </div>

                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button @click="openFaq = (openFaq === 7 ? null : 7)"
                                class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <span>Khi nào chủ sân nhận được tiền từ các đơn đặt online?</span>
                                <svg class="w-4 h-4 text-zinc-400 transform transition-transform" :class="{'rotate-180': openFaq === 7}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openFaq === 7" x-collapse class="px-4 pb-4 text-sm text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 pt-3 leading-relaxed">
                                Tiền đặt sân từ khách hàng thanh toán trực tuyến sẽ được ghi nhận vào báo cáo doanh thu của chủ sân và được quyết toán định kỳ theo chu kỳ thỏa thuận (hàng tuần hoặc hàng tháng) vào tài khoản ngân hàng của chủ sân.
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
