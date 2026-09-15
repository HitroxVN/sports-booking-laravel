<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Khởi tạo theme (sáng/tối) trước khi render để tránh nhấp nháy FOUC --}}
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('color-mode');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) { /* bỏ qua nếu localStorage bị chặn */ }
        })();
    </script>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo/logo.jpg') }}">

    <!-- Fonts: Inter — hệ typography Long Châu -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Alpine.js được bundle cùng app.js (Vite) — đăng ký component bookingGrid trước khi Alpine start -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 flex flex-col min-h-screen">

    {{-- Header dùng chung (utility bar + navbar) — component: components/site-header.blade.php --}}
    <x-site-header />

    {{-- Flash messages giờ do <x-lc-toast /> (góc trên phải) hiển thị — xem components/lc-toast.blade.php --}}

    {{-- ================================================
         PAGE CONTENT
    ================================================= --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer dùng chung — component: components/site-footer.blade.php --}}
    <x-site-footer />

    {{-- Dòng chữ khẩu hiệu chạy ngang, cố định dưới cùng màn hình --}}
    <x-lc-marquee />

    {{-- Livechat Realtime Floating Widget --}}
    <x-chat-widget />

    {{-- Toast/Snackbar: flash session + thông báo JS (window.toast) — góc trên phải --}}
    <x-lc-toast />

    @stack('scripts')
</body>

</html>