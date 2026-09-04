{{-- ================================================================
     SITE FOOTER — dùng chung mọi trang khách (layouts/customer)
     Chỉnh sửa giao diện footer chỉ cần sửa file này.
     Được dùng qua: <x-site-footer />
================================================================= --}}
<footer class="bg-zinc-900 dark:bg-zinc-950 text-zinc-400 dark:text-zinc-500 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        {{-- Top row: brand + links --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-zinc-800">

            {{-- Brand column --}}
            <div class="lg:col-span-1">
                <a href="/" class="flex items-center gap-2.5 mb-4 group">
                    <img src="{{ asset('images/logo/logo.jpg') }}" alt="Arena Sports Booking"
                        class="w-9 h-9 rounded-lg object-cover shrink-0">
                    <div class="leading-tight">
                        <span class="block text-sm font-bold text-white tracking-tight">Arena</span>
                        <span class="block text-[10px] font-medium text-zinc-500 uppercase tracking-widest">Sports Booking</span>
                    </div>
                </a>
                <p class="text-sm leading-relaxed text-zinc-500 mb-5 max-w-xs">
                    Nền tảng đặt sân thể thao trực tuyến hàng đầu Việt Nam. Kết nối người chơi với hơn 200 khu sân chất lượng.
                </p>
                {{-- Social links --}}
                <div class="flex items-center gap-3">
                    <a href="#" aria-label="Facebook" class="w-8 h-8 bg-zinc-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                    </a>
                    <a href="#" aria-label="YouTube" class="w-8 h-8 bg-zinc-800 hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                            <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="w-8 h-8 bg-zinc-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke-width="2" />
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Người dùng --}}
            <div>
                <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Người dùng</h3>
                <ul class="space-y-2.5">
                    <li><a href="/search" class="text-sm hover:text-white transition-colors">Tìm kiếm sân</a></li>
                    <li><a href="/register" class="text-sm hover:text-white transition-colors">Đăng ký tài khoản</a></li>
                    <li><a href="/login" class="text-sm hover:text-white transition-colors">Đăng nhập</a></li>
                    <li><a href="/my-bookings" class="text-sm hover:text-white transition-colors">Đơn đặt sân</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Hướng dẫn sử dụng</a></li>
                </ul>
            </div>

            {{-- Chủ sân --}}
            <div>
                <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Chủ sân</h3>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Đăng ký khu sân</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Quản lý lịch đặt</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Báo cáo doanh thu</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Khuyến mãi</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách hợp tác</a></li>
                </ul>
            </div>

            {{-- Hỗ trợ --}}
            <div>
                <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">Hỗ trợ</h3>
                <ul class="space-y-2.5">
                    <li><a href="/lien-he" class="text-sm hover:text-white transition-colors">Liên hệ với chúng tôi</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách hủy sân</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách thanh toán</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Chính sách bảo mật</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom row --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-zinc-600 dark:text-zinc-600">
                &copy; {{ date('Y') }} Arena Sports Booking. Tất cả quyền được bảo lưu.
            </p>
            <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-600">
                <span>Hotline:</span>
                <a href="tel:19001234" class="hover:text-zinc-400 transition-colors">1900 1234</a>
                <span class="select-none">&middot;</span>
                <span>Email:</span>
                <a href="mailto:hotro@arenasports.vn" class="hover:text-zinc-400 transition-colors">hotro@arenasports.vn</a>
            </div>
        </div>
    </div>
</footer>