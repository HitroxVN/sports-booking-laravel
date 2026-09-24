<?php

// Helper toàn cục: setting('contact_hotline', '1900 1234')
if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        return App\Models\Setting::get($key, $default);
    }
}

// Định dạng giờ hỗ trợ từ JSON {days, open, close} thành text hiển thị.
// Giá trị cũ dạng text thuần (không parse được JSON) được trả nguyên vẹn.
if (! function_exists('format_contact_hours')) {
    function format_contact_hours(?string $json, string $fallback = 'Thứ 2 – Chủ nhật, 8:00 – 23:00'): string
    {
        $data = $json ? json_decode($json, true) : null;
        if (! is_array($data) || empty($data['days'])) {
            return $json ?: $fallback;
        }

        $names = [0 => 'Chủ nhật', 1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7'];
        $days = collect(array_map('intval', $data['days']))->sort()->values();

        if ($days->count() === 7) {
            $dayText = 'Thứ 2 – Chủ nhật';
        } elseif ($days->count() > 1 && $days->every(fn ($d, $i) => $i === 0 || $d === $days[$i - 1] + 1)) {
            $dayText = $names[$days->first()] . ' – ' . $names[$days->last()];
        } else {
            $dayText = $days->map(fn ($d) => $names[$d])->implode(', ');
        }

        return $dayText . ', ' . $data['open'] . ' – ' . $data['close'];
    }
}
