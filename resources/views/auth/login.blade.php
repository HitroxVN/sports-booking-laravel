<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h1 class="text-xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ __('Chào mừng quay lại') }}</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Đăng nhập vào tài khoản của bạn để tiếp tục') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-zinc-900" name="remember">
                <span class="ms-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Ghi nhớ tôi') }}</span>
            </label>

            @if (Route::has('password.request'))
            <a class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-zinc-900" href="{{ route('password.request') }}">
                {{ __('Quên mật khẩu?') }}
            </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full">
                {{ __('Đăng nhập') }}
            </x-primary-button>
        </div>
        <div class="mt-4 text-center">
            <a class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-zinc-900" href="{{ route('register') }}">
                {{ __('Chưa có tài khoản?') }}
            </a>
        </div>
    </form>
</x-guest-layout>