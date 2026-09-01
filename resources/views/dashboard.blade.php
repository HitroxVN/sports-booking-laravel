@extends('layouts.customer')

@section('title', 'Trang cá nhân')

@section('content')
    <div class="container py-12 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto card-base p-8 text-center">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Chào mừng, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                Bạn đã đăng nhập thành công. Bắt đầu tìm kiếm và đặt sân thể thao ngay thôi.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="/search" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    Tìm sân ngay
                </a>
                <a href="/my-bookings" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold rounded-lg border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                    Đơn đặt sân của tôi
                </a>
            </div>
        </div>
    </div>
@endsection
