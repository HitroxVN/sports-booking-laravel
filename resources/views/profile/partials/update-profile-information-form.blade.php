<section>
    <header>
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
            Thông tin cá nhân
        </h2>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Cập nhật thông tin liên lạc của bạn. Email dùng để đăng nhập và nhận thông báo đặt sân.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- Ảnh đại diện --}}
        <div x-data="{
                preview: @js($user->avatar ? asset('storage/' . $user->avatar) : null),
                pick(e) {
                    const file = e.target.files[0];
                    if (file) this.preview = URL.createObjectURL(file);
                }
             }">
            <x-input-label :value="'Ảnh đại diện'" />
            <div class="mt-2 flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-primary-600 text-white flex items-center justify-center overflow-hidden shrink-0 ring-1 ring-zinc-200 dark:ring-zinc-700">
                    <img x-show="preview" x-cloak x-bind:src="preview" alt="Xem trước ảnh đại diện" class="w-full h-full object-cover">
                    <span x-show="!preview" x-cloak class="text-2xl font-extrabold uppercase">{{ mb_substr(trim($user->name), 0, 1) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <input type="file" name="avatar" accept="image/*" @change="pick($event)"
                        class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 transition cursor-pointer border border-zinc-200 dark:border-zinc-700 rounded-xl">
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1.5">JPG, PNG hoặc WebP. Tối đa 2MB.</p>
                    @if ($user->avatar)
                        <label class="mt-1.5 inline-flex items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400 cursor-pointer select-none">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500/30">
                            Gỡ ảnh hiện tại
                        </label>
                    @endif
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="'Họ và tên'" />
            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" placeholder="VD: Nguyễn Văn A" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="phone" :value="'Số điện thoại'" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-2 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="VD: 0912 345 678" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="email" :value="'Địa chỉ email'" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" placeholder="VD: email@example.com" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 flex items-start gap-2 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-amber-800 dark:text-amber-300">Email của bạn chưa được xác thực.</p>
                        <button form="send-verification" class="mt-1 underline font-medium text-amber-800 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-200 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                            Gửi lại email xác thực
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-green-600 dark:text-green-400">
                                Đã gửi lại email xác thực, vui lòng kiểm tra hộp thư của bạn.
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-1">
            <x-primary-button>Lưu thay đổi</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-green-600 dark:text-green-400"
                >Đã lưu thay đổi.</p>
            @endif
        </div>
    </form>
</section>
