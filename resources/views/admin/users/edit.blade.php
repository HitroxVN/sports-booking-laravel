<x-admin-layout :title="'Sửa người dùng'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Sửa người dùng</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cập nhật thông tin tài khoản của {{ $user->name }}</p>
    </div>

    <div class="max-w-2xl space-y-6">

        {{-- Card 1: Thông tin --}}
        <div class="card-base p-6">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Thông tin tài khoản</h3>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="label-eyebrow block mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                           class="input-base @error('name') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="label-eyebrow block mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                           class="input-base @error('email') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @if($user->email_verified_at)
                        <p class="mt-1 text-xs text-green-600 dark:text-green-400">✓ Email đã xác thực</p>
                    @else
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">Email chưa xác thực — sẽ phải xác thực lại nếu thay đổi</p>
                    @endif
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="label-eyebrow block mb-1">Số điện thoại</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                               placeholder="VD: 0912345678"
                               class="input-base @error('phone') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="role" class="label-eyebrow block mb-1">Vai trò <span class="text-red-500">*</span></label>
                        <select id="role" name="role" class="input-base">
                            <option value="customer" @selected(old('role', $user->role) === 'customer')>Khách hàng</option>
                            <option value="owner" @selected(old('role', $user->role) === 'owner')>Chủ sân</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="status" class="label-eyebrow block mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="input-base">
                        <option value="active" @selected(old('status', $user->status) === 'active')>Hoạt động</option>
                        <option value="banned" @selected(old('status', $user->status) === 'banned')>Bị khóa</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>

        {{-- Card 2: Đặt lại mật khẩu --}}
        <div class="card-base p-6">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Đặt lại mật khẩu</h3>
            <form method="POST" action="{{ route('admin.users.password', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new-password" class="label-eyebrow block mb-1">Mật khẩu mới <span class="text-red-500">*</span></label>
                        <input id="new-password" type="password" name="password" required autocomplete="new-password"
                               class="input-base @error('password') border-red-300 dark:border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="new-password-confirmation" class="label-eyebrow block mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                        <input id="new-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="input-base">
                    </div>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Người dùng sẽ dùng mật khẩu mới ở lần đăng nhập sau. Hệ thống không hiển thị mật khẩu hiện tại.
                </p>

                <button type="submit" class="btn-primary">Đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
</x-admin-layout>
