<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::withCount('courts')->orderBy('name')->get();
        return view('admin.sports.index', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sports',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('sports', 'public');
        }
        Sport::create(array_merge($data, ['is_active' => true]));
        return back()->with('success', 'Đã thêm môn thể thao.');
    }

    public function update(Request $request, Sport $sport)
    {
        // validateWithBag khớp với error bag 'sport_'.$id trong view inline edit
        $data = $request->validateWithBag('sport_' . $sport->id, [
            'name'      => "required|string|max:100|unique:sports,name,{$sport->id}",
            'icon'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);
        // Chỉ thay ảnh nếu admin upload ảnh mới; không chọn file = giữ ảnh cũ
        if ($request->hasFile('icon')) {
            // Xóa ảnh cũ
            if ($sport->icon && Storage::disk('public')->exists($sport->icon)) {
                Storage::disk('public')->delete($sport->icon);
            }
            $data['icon'] = $request->file('icon')->store('sports', 'public');
        }
        $sport->update($data);
        return back()->with('success', 'Đã cập nhật môn thể thao.');
    }

    public function destroy(Sport $sport)
    {
        // Chặn xóa nếu còn sân con đang dùng — dùng flash error thay vì abort 422
        if ($sport->courts()->exists()) {
            return back()->with('error', 'Không thể xóa môn thể thao đang có sân con.');
        }
        // Xóa cả ảnh của sport 
        if ($sport->icon && Storage::disk('public')->exists($sport->icon)) {
            Storage::disk('public')->delete($sport->icon);
        }
        $sport->delete();
        return back()->with('success', 'Đã xóa môn thể thao.');
    }
}
