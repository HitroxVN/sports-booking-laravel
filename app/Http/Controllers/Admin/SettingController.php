<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Trang cấu hình chung của hệ thống (logo, thông tin liên hệ...).
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Lưu toàn bộ cấu hình — logo upload riêng, các trường text ghi thẳng.
     */
    public function update(Request $request)
    {
        // 1. Các trường text
        $textKeys = [
            'site_name',           // tên brand cạnh logo (vd: Arena)
            'site_tagline',        // dòng phụ dưới tên brand (vd: Sports Booking)
            'contact_hotline',     // hotline hiển thị; tel: tự strip khoảng trắng
            'contact_email',
            'contact_address',
            'footer_description',
            'social_facebook',
            'social_zalo',
        ];
        foreach ($textKeys as $key) {
            Setting::set($key, $request->input($key));
        }

        // 2. Giờ hỗ trợ: ngày trong tuần + khung giờ → lưu JSON, hiển thị qua format_contact_hours()
        $validated = $request->validate([
            'support_days'  => 'required|array|min:1',
            'support_days.*' => 'integer|between:0,6',
            'support_open'  => 'required|date_format:H:i',
            'support_close' => 'required|date_format:H:i|after:support_open',
        ]);
        Setting::set('contact_hours', json_encode([
            'days'  => array_map('intval', $validated['support_days']),
            'open'  => $validated['support_open'],
            'close' => $validated['support_close'],
        ]));

        // 3. Logo: upload mới thì xóa file cũ (chỉ file do upload tạo ra)
        if ($request->hasFile('app_logo')) {
            $request->validate(['app_logo' => 'image|mimes:jpeg,png,jpg,webp,svg|max:2048']);
            Setting::deleteFile(Setting::get('app_logo'));
            Setting::set('app_logo', $request->file('app_logo')->store('settings', 'public'));
        }

        return back()->with('success', 'Đã lưu cấu hình hệ thống.');
    }
}
