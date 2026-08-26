<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    /**
     * Hiển thị danh sách khu sân của chủ sân đang đăng nhập.
     */
    public function index()
    {
        $venues = Venue::where('owner_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('owner.venues.index', compact('venues'));
    }

    /**
     * Hiển thị form thêm khu sân mới.
     */
    public function create()
    {
        return view('owner.venues.create');
    }

    /**
     * Lưu thông tin khu sân mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'city'        => 'required|string|max:100',
            'district'    => 'required|string|max:100',
            'ward'        => 'nullable|string|max:100',
            'address'     => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'amenities'   => 'nullable|array',
        ]);

        $validated['owner_id'] = auth()->id();
        $validated['status'] = 'pending'; // Trạng thái mặc định: chờ admin duyệt

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('venues', 'public');
        }

        Venue::create($validated);

        return redirect()->route('owner.venues.index')
            ->with('success', 'Thêm khu sân thành công. Vui lòng chờ hệ thống duyệt!');
    }

    /**
     * Xem chi tiết một khu sân.
     */
    public function show(Venue $venue)
    {
        $this->authorizeOwnership($venue);
        
        // Eager load các dữ liệu liên quan để tối ưu truy vấn
        $venue->load(['images', 'courts', 'operatingHours']);
        
        return view('owner.venues.show', compact('venue'));
    }

    /**
     * Hiển thị form cập nhật thông tin khu sân.
     */
    public function edit(Venue $venue)
    {
        $this->authorizeOwnership($venue);

        return view('owner.venues.edit', compact('venue'));
    }

    /**
     * Xử lý lưu thông tin cập nhật của khu sân.
     */
    public function update(Request $request, Venue $venue)
    {
        $this->authorizeOwnership($venue);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'district'    => 'required|string|max:100',
            'city'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,pending,closed',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Cập nhật thông tin cơ bản
        $venue->update($validated);

        // Xử lý upload ảnh vào bảng venue_images
        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('venues', 'public');

            // Xóa ảnh cũ trong venue_images (nếu có) để tránh rác, rồi tạo bản ghi mới
            if ($venue->images()->exists()) {
                foreach ($venue->images()->get() as $img) {
                    if (Storage::disk('public')->exists($img->path)) {
                        Storage::disk('public')->delete($img->path);
                    }
                    $img->delete();
                }
            }

            // Tạo mới bản ghi ảnh liên kết với khu sân này
            $venue->images()->create([
                'path'       => $filePath,
                'sort_order' => 1,
            ]);
        }

        return redirect()->route('owner.venues.show', $venue)->with('success', 'Cập nhật thông tin khu sân thành công!');
    }

    /**
     * Xóa khu sân (Sử dụng Soft Delete).
     */
    public function destroy(Venue $venue)
    {
        $this->authorizeOwnership($venue);

        // Vì Model có dùng SoftDeletes, hàm delete() sẽ chỉ đánh dấu deleted_at chứ không xóa hẳn
        $venue->delete();

        return redirect()->route('owner.venues.index')
            ->with('success', 'Đã xóa khu sân thành công!');
    }

    /**
     * Hàm dùng chung để chặn các chủ sân sửa sân của người khác.
     */
    private function authorizeOwnership(Venue $venue)
    {
        abort_if(
            $venue->owner_id !== auth()->id(), 
            403, 
            'Bạn không có quyền thao tác trên khu sân này.'
        );
    }
}