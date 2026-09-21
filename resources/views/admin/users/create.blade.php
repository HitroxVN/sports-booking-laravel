<x-admin-layout :title="'Thêm người dùng'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Thêm người dùng</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tạo tài khoản khách hàng hoặc chủ sân mới</p>
    </div>

    <div class="max-w-2xl">
        <div class="card-base p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="label-eyebrow block mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                           placeholder="VD: Nguyễn Văn A"
                           class="input-base @error('name') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="label-eyebrow block mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                           placeholder="VD: user@example.com"
                           class="input-base @error('email') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="label-eyebrow block mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="input-base @error('password') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="label-eyebrow block mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="input-base">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="label-eyebrow block mb-1">Số điện thoại</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="20"
                               placeholder="VD: 0912345678"
                               class="input-base @error('phone') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="role" class="label-eyebrow block mb-1">Vai trò <span class="text-red-500">*</span></label>
                        <select id="role" name="role" class="input-base">
                            <option value="customer" @selected(old('role') === 'customer')>Khách hàng</option>
                            <option value="owner" @selected(old('role') === 'owner')>Chủ sân</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Tài khoản được admin tạo trực tiếp sẽ không cần xác thực email.
                </p>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Tạo tài khoản</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
