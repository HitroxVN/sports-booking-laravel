<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/logo.jpg') }}">

    <title>{{ config('app.name', 'Arena Sports Booking') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">
    <div class="min-h-screen flex flex-col">

        {{-- Logo top — đồng bộ wordmark với x-site-header --}}
        <header class="pt-8 pb-4 flex justify-center">
            <a href="/" class="flex items-center gap-2.5">
                <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Arena Sports Booking"
                    class="w-9 h-9 rounded-lg object-cover shrink-0">
                <div class="leading-tight">
                    <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Arena</span>
                    <span class="block text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Sports Booking</span>
                </div>
            </a>
        </header>

        {{-- Auth card --}}
        <main class="flex-1 flex items-start sm:items-center justify-center px-4 py-6">
            <div class="w-full sm:max-w-md bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 sm:p-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer dùng chung — component: components/site-footer.blade.php --}}
        <x-site-footer />
    </div>

    @stack('scripts')
</body>

</html>
