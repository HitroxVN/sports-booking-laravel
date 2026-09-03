{{--
    CONTACT SECTION — "Liên hệ với chúng tôi" dùng chung mọi trang khách
    Thông tin công ty + bản đồ Google Maps (embed, không cần API key).
    Được dùng qua: <x-contact-section />
    Muốn đổi thông tin công ty: sửa các biến trong PHP block bên dưới.
--}}
@php
    $contact = [
        'address' => 'Đại học Tài nguyên và Môi trường Hà Nội',
        'phone' => '0999 986 866',
        'phoneRaw' => '0999986866',
        'email' => 'hotro@arenasports.vn',
        'hours' => 'Thứ 2 – Chủ nhật, 8:00 – 23:00',
    ];
@endphp

<section class="bg-zinc-50 dark:bg-zinc-950 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Tiêu đề --}}
        <div class="max-w-2xl mb-12">
            <p class="label-eyebrow mb-3">Liên hệ với chúng tôi</p>
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Luôn sẵn sàng hỗ trợ bạn
            </h2>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Bạn có thắc mắc về đặt sân, hợp tác khu sân hay cần hỗ trợ khẩn cấp?
                Hãy liên hệ ngay với đội ngũ Arena — chúng tôi phản hồi trong giờ làm việc.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Cột trái: Thông tin liên hệ --}}
            <div class="card-base p-8">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    Thông Tin Liên Hệ
                </h3>

                <ul class="space-y-6">
                    {{-- Địa chỉ --}}
                    <li class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="label-eyebrow mb-1">Địa chỉ</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed">{{ $contact['address'] }}</p>
                        </div>
                    </li>

                    {{-- Điện thoại --}}
                    <li class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.361-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="label-eyebrow mb-1">Điện thoại</p>
                            <a href="tel:{{ $contact['phoneRaw'] }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                                {{ $contact['phone'] }}
                            </a>
                        </div>
                    </li>

                    {{-- Email --}}
                    <li class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="label-eyebrow mb-1">Email</p>
                            <a href="mailto:{{ $contact['email'] }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline break-all">
                                {{ $contact['email'] }}
                            </a>
                        </div>
                    </li>

                    {{-- Giờ làm việc --}}
                    <li class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="label-eyebrow mb-1">Giờ làm việc</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $contact['hours'] }}</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Cột phải: Bản đồ --}}
            <div class="card-base p-2 overflow-hidden">
                <iframe
                    src="https://maps.google.com/maps?q={{ urlencode('Đại học Tài nguyên và Môi trường Hà Nội') }}&output=embed"
                    class="w-full h-full min-h-[420px] rounded-2xl border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Bản đồ — Đại học Tài nguyên và Môi trường Hà Nội"
                    allowfullscreen
                ></iframe>
            </div>

        </div>
    </div>
</section>
