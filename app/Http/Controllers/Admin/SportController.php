<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;

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
            'icon' => 'nullable|string|max:10',
        ]);
        Sport::create(array_merge($data, ['is_active' => true]));
        return back()->with('success', 'Đã thêm môn thể thao.');
    }

    public function update(Request $request, Sport $sport)
    {
        $data = $request->validate([
            'name'      => "required|string|max:100|unique:sports,name,{$sport->id}",
            'icon'      => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);
        $sport->update($data);
        return back()->with('success', 'Đã cập nhật môn thể thao.');
    }

    public function destroy(Sport $sport)
    {
        // Chặn xóa nếu còn sân con đang dùng — dùng flash error thay vì abort 422
        if ($sport->courts()->exists()) {
            return back()->with('error', 'Không thể xóa môn thể thao đang có sân con.');
        }
        $sport->delete();
        return back()->with('success', 'Đã xóa môn thể thao.');
    }
}
