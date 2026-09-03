@extends('layouts.customer')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Tiêu đề trang --}}
        <div class="mb-8 border-b border-zinc-200 dark:border-zinc-700 pb-5">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">Hồ sơ cá nhân</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Quản lý thông tin tài khoản và bảo mật của bạn.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- ================================================
                 CỘT TRÁI: THẺ TÓM TẮT TÀI KHOẢN
            ================================================= --}}
            <aside class="lg:sticky lg:top-24 space-y-6">
                <div class="card-base overflow-hidden">
                    {{-- Băng nền xanh thương hiệu --}}
                    <div class="h-24 bg-gradient-to-r from-primary-700 to-primary-500"></div>

                    <div class="px-6 pb-6 -mt-10">
                        {{-- Avatar: ưu tiên ảnh đã tải lên, fallback là chữ cái đầu --}}
                        <div class="w-20 h-20 rounded-2xl ring-4 ring-white dark:ring-zinc-900 bg-primary-600 text-white flex items-center justify-center overflow-hidden shadow-md">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-extrabold uppercase">{{ mb_substr(trim($user->name), 0, 1) }}</span>
                            @endif
                        </div>

                        <h2 class="mt-3 text-lg font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $user->name }}</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">{{ $user->email }}</p>

                        <div class="mt-3">
                            @if ($user->role === 'admin')
                                <x-badge variant="info">Quản trị viên</x-badge>
                            @elseif ($user->role === 'owner')
                                <x-badge variant="success">Chủ sân</x-badge>
                            @else
                                <x-badge>Khách hàng</x-badge>
                            @endif
                        </div>

                        {{-- Thông tin nhanh --}}
                        <dl class="mt-5 pt-5 border-t border-zinc-100 dark:border-zinc-800 space-y-3 text-sm">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 mt-0.5 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Số điện thoại</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $user->phone ?? 'Chưa cập nhật' }}</dd>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 mt-0.5 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Thành viên từ</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</dd>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 mt-0.5 shrink-0 {{ $user->hasVerifiedEmail() ? 'text-green-500' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if ($user->hasVerifiedEmail())
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    @endif
                                </svg>
                                <div>
                                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Email</dt>
                                    <dd class="font-medium {{ $user->hasVerifiedEmail() ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $user->hasVerifiedEmail() ? 'Đã xác thực' : 'Chưa xác thực' }}
                                    </dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>
            </aside>

            {{-- ================================================
                 CỘT PHẢI: CÁC FORM
            ================================================= --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- 1. Thông tin cá nhân --}}
                <div class="card-base p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- 2. Đổi mật khẩu --}}
                <div class="card-base p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                {{-- 3. Vùng nguy hiểm --}}
                <div class="card-base p-6 sm:p-8 border-red-200 dark:border-red-900/50">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
