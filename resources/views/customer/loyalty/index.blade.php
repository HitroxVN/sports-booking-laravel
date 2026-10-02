@extends('layouts.customer')

@section('title', 'Điểm tích luỹ & Đổi voucher')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Tiêu đề trang & Tóm tắt điểm --}}
    <div class="mb-8 border-b border-zinc-200 dark:border-zinc-700 pb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight flex items-center gap-2.5">
                    <span>Điểm tích luỹ & Đổi voucher</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                        {{ number_format($user->points) }} điểm
                    </span>
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Theo dõi điểm thưởng, đổi voucher ưu đãi và lịch sử giao dịch điểm của bạn.</p>
            </div>
            <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Đơn đặt sân của tôi
            </a>
        </div>
    </div>

    @include('profile.partials.loyalty-program')
</div>
@endsection
