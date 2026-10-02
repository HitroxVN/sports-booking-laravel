@extends('layouts.customer')

@section('title', 'Chính sách bảo mật — ' . config('app.name', 'Arena'))

@section('content')
<div class="bg-zinc-50 dark:bg-zinc-950 py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('help') }}" class="hover:text-primary-600 transition-colors">Hỗ trợ</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Chính sách bảo mật</span>
        </nav>

        {{-- Hero Header --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-8 sm:p-12 mb-10 shadow-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Bảo vệ dữ liệu cá nhân
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight mb-3">
                Chính Sách Bảo Mật Quyền Riêng Tư
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base max-w-3xl leading-relaxed">
                Chúng tôi tôn trọng và cam kết bảo vệ dữ liệu cá nhân của người dùng theo Nghị định 13/2023/NĐ-CP về Bảo vệ dữ liệu cá nhân và các quy định pháp luật hiện hành của Việt Nam.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <x-policy-sidebar active="privacy" />

            {{-- Nội dung chi tiết --}}
            <main class="lg:col-span-3 space-y-8 text-zinc-800 dark:text-zinc-200">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            1. Dữ liệu chúng tôi thu thập
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-3">
                            Khi bạn đăng ký tài khoản và thực hiện đặt sân trên Arena, chúng tôi chỉ thu thập các thông tin tối thiểu cần thiết để phục vụ cung cấp dịch vụ:
                        </p>
                        <ul class="list-disc list-inside space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            <li><strong>Thông tin cơ bản:</strong> Họ tên, số điện thoại, địa chỉ email, ảnh đại diện (nếu bạn cung cấp).</li>
                            <li><strong>Lịch sử giao dịch:</strong> Các đơn đặt sân, khung giờ thi đấu, phương thức thanh toán và điểm thưởng tích lũy.</li>
                            <li><strong>Dữ liệu kỹ thuật:</strong> Địa chỉ IP, loại thiết bị và trình duyệt để nâng cao chất lượng bảo mật phiên đăng nhập.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            2. Mục đích và phạm vi sử dụng thông tin
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Thông tin của bạn được sử dụng nhằm: Xác nhận đơn đặt sân, liên lạc trong các trường hợp thay đổi lịch bất ngờ, cung cấp cho chủ sân đúng mã số để bạn nhận sân, và gửi thông báo xác thực bảo mật tài khoản. Arena <strong>cam kết tuyệt đối không bán hoặc chia sẻ thông tin cá nhân cho bên thứ ba vì mục đích tiếp thị thương mại</strong>.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold mb-3 flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                            <span class="w-2.5 h-6 bg-primary-600 rounded-full"></span>
                            3. Quyền của khách hàng đối với thông tin cá nhân
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Bạn có toàn quyền truy cập trang <strong>Hồ sơ cá nhân</strong> để xem, chỉnh sửa thông tin, đổi mật khẩu hoặc yêu cầu khóa / xóa tài khoản vĩnh viễn khỏi hệ thống bất cứ lúc nào. Mọi yêu cầu hỗ trợ dữ liệu có thể gửi về email <strong>{{ setting('contact_email', 'hotro@arenasports.vn') }}</strong>.
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
