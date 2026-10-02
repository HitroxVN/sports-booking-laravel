@extends('layouts.customer')

@section('title', 'Điều khoản sử dụng — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('help') }}" class="hover:text-primary-600 transition-colors">Hỗ trợ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Điều khoản sử dụng</span>
        </nav>

        {{-- Hero Header --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-8 sm:p-12 mb-10 shadow-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 dark:bg-primary-950/60 text-primary-800 dark:text-primary-300 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Thỏa thuận dịch vụ
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight mb-3">
                Điều Khoản & Điều Kiện Sử Dụng
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base max-w-3xl leading-relaxed">
                Chào mừng bạn đến với nền tảng đặt sân thể thao Arena. Khi sử dụng dịch vụ của chúng tôi, bạn đồng ý tuân thủ các điều khoản và quy định dưới đây.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <x-policy-sidebar active="terms" />

            {{-- Nội dung chi tiết --}}
            <main class="lg:col-span-3 space-y-8 text-zinc-800 dark:text-zinc-200">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            1. Định nghĩa và Phạm vi áp dụng
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Arena là nền tảng công nghệ trung gian kết nối người chơi có nhu cầu thuê sân thể thao với các chủ cơ sở kinh doanh sân bãi (bóng đá, cầu lông, pickleball, tennis,...). Arena cung cấp hạ tầng tra cứu, quản lý lịch và thanh toán tiện lợi cho hai bên.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            2. Quyền và nghĩa vụ của Người đặt sân
                        </h2>
                        <ul class="list-disc list-inside space-y-2 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            <li>Cung cấp thông tin số điện thoại, tên chính xác để nhận mã đơn và nhận diện khi đến sân.</li>
                            <li>Đến sân đúng khung giờ đã đặt. Tôn trọng nội quy vệ sinh, giữ gìn tài sản và văn hóa thể thao tại sân.</li>
                            <li>Chịu trách nhiệm bảo quản tư trang cá nhân và đảm bảo an toàn thể lực khi tham gia tập luyện thi đấu.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            3. Quyền và nghĩa vụ của Chủ sân đối tác
                        </h2>
                        <ul class="list-disc list-inside space-y-2 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            <li>Cung cấp sân thi đấu đúng chất lượng, đúng khung giờ và đúng môn thể thao đã cam kết trên hệ thống.</li>
                            <li>Cập nhật tình trạng sân, bảng giá minh bạch và thông báo kịp thời khi có kế hoạch bảo trì.</li>
                            <li>Không được tự ý hủy đơn của khách khi chưa có thỏa thuận hoặc sự đồng ý từ khách hàng.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            4. Đánh giá và bình luận
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Chỉ những khách hàng đã thực sự hoàn thành đơn đặt sân mới có quyền để lại đánh giá và nhận xét. Nghiêm cấm các hành vi dùng từ ngữ tục tĩu, công kích cá nhân hoặc bôi nhọ đối thủ cạnh tranh. Arena có quyền ẩn hoặc gỡ bỏ các đánh giá vi phạm quy chuẩn cộng đồng.
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
