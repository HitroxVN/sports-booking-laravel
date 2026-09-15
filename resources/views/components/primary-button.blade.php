@php
    $isLink = $attributes->has('href');
@endphp

@if ($isLink)
    {{-- Link variant (vd: empty-state action) — tránh render <button> chết trong <a> --}}
    <a href="{{ $attributes->get('href') }}" {{ $attributes->except(['href', 'type'])->merge(['class' => 'btn-primary inline-flex items-center justify-center']) }}>
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary']) }}
        {{-- Chống double-submit: đăng ký listener submit cho form chứa nút (onsubmit trên <button> không bao giờ fire) --}}
        onclick="var f = this.closest('form'), b = this; if (f && !f.dataset.submitGuard) { f.dataset.submitGuard = '1'; f.addEventListener('submit', function () { b.disabled = true; b.classList.add('opacity-50','cursor-not-allowed'); }); }"
    >
        {{ $slot }}
    </button>
@endif
