<x-admin-layout :title="'Cấu hình hệ thống'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Cấu hình hệ thống</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Logo, thông tin liên hệ hiển thị trên toàn bộ website khách.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        {{-- Logo & thương hiệu --}}
        <div class="card-base p-6 sm:p-8">
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Logo &amp; thương hiệu</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="label-eyebrow block mb-2">Tên thương hiệu</label>
                    <input type="text" name="site_name" value="{{ setting('site_name', 'Arena') }}" class="input-base">
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Dòng chính cạnh logo.</p>
                </div>
                <div>
                    <label class="label-eyebrow block mb-2">Tagline</label>
                    <input type="text" name="site_tagline" value="{{ setting('site_tagline', 'Sports Booking') }}" class="input-base">
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Dòng phụ dưới tên thương hiệu.</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <img src="{{ \App\Models\Setting::asset('app_logo', asset('images/logo/logo.jpg')) }}" alt="Logo hiện tại"
                     class="w-20 h-20 rounded-2xl object-cover border border-zinc-200 dark:border-zinc-700">
                <div class="flex-1">
                    <label class="label-eyebrow block mb-2">Tải logo mới</label>
                    <input type="file" name="app_logo" accept="image/jpeg,image/png,image/webp,image/svg+xml"
                           class="input-base file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-600 dark:file:text-primary-400 file:text-sm file:font-semibold">
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">JPG, PNG, WebP hoặc SVG, tối đa 2MB. Áp dụng cho header, footer, sidebar, favicon.</p>
                    @error('app_logo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Thông tin liên hệ --}}
        <div class="card-base p-6 sm:p-8">
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Thông tin liên hệ</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label-eyebrow block mb-2">Hotline</label>
                    <input type="text" name="contact_hotline" value="{{ setting('contact_hotline', '1900 1234') }}" class="input-base">
                </div>
                <div>
                    <label class="label-eyebrow block mb-2">Email hỗ trợ</label>
                    <input type="email" name="contact_email" value="{{ setting('contact_email', 'hotro@arenasports.vn') }}" class="input-base">
                </div>
                <div class="sm:col-span-2">
                    <label class="label-eyebrow block mb-2">Giờ hỗ trợ</label>
                    @php
                        $hoursData = json_decode(setting('contact_hours') ?? '', true) ?: ['days' => [1,2,3,4,5,6,0], 'open' => '08:00', 'close' => '23:00'];
                        $dayNames = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 0 => 'Chủ nhật'];
                    @endphp
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        @foreach($dayNames as $d => $name)
                        <label class="inline-flex items-center gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
                            <input type="checkbox" name="support_days[]" value="{{ $d }}"
                                   @checked(in_array($d, $hoursData['days']))
                                   class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500">
                            {{ $name }}
                        </label>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <input type="time" name="support_open" value="{{ $hoursData['open'] }}" class="input-base w-auto text-sm">
                        <span class="text-zinc-400">–</span>
                        <input type="time" name="support_close" value="{{ $hoursData['close'] }}" class="input-base w-auto text-sm">
                    </div>
                    @error('support_days.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @error('support_close') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="label-eyebrow block mb-2">Địa chỉ</label>
                    <input type="text" name="contact_address" value="{{ setting('contact_address', 'Đại học Tài nguyên và Môi trường Hà Nội') }}" class="input-base">
                </div>
            </div>
        </div>

        {{-- Footer & mạng xã hội --}}
        <div class="card-base p-6 sm:p-8">
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">Footer &amp; mạng xã hội</h3>
            <div class="space-y-5">
                <div>
                    <label class="label-eyebrow block mb-2">Mô tả footer</label>
                    <textarea name="footer_description" rows="2" class="input-base">{{ setting('footer_description', 'Nền tảng đặt sân thể thao trực tuyến hàng đầu Việt Nam. Kết nối người chơi với hơn 200 khu sân chất lượng.') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="label-eyebrow block mb-2">Link Facebook</label>
                        <input type="url" name="social_facebook" value="{{ setting('social_facebook') }}" placeholder="https://facebook.com/..." class="input-base">
                    </div>
                    <div>
                        <label class="label-eyebrow block mb-2">Link Zalo</label>
                        <input type="url" name="social_zalo" value="{{ setting('social_zalo') }}" placeholder="https://zalo.me/..." class="input-base">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Lưu cấu hình</button>
        </div>
    </form>
</x-admin-layout>
