<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OperatingHour;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    // 0=Chủ nhật ... 6=Thứ 7 — khớp cột day_of_week
    private const DAY_NAMES = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];

    /**
     * Hiển thị danh sách khu sân của chủ sân đang đăng nhập.
     */
    public function index()
    {
        $venues = Venue::withCount('courts')
            ->where('owner_id', auth()->id())
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
            'ward'        => 'required|string|max:100',
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
            'city'        => 'required|string|max:100',
            'ward'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'amenities'   => 'nullable|array',
            'amenities.*' => 'in:wifi,parking,canteen,changing_room,shower,air_conditioner',
        ]);

        // Checkbox bỏ tick hết = không có key amenities → ép về mảng rỗng để xoá tiện ích cũ
        $validated['amenities'] = $request->input('amenities', []);

        // Trạng thái sân chỉ admin được quyết định (duyệt/từ chối).
        // Chủ sân chỉ có thể tạm đóng/mở lại sân đang hoạt động — không tự "duyệt" sân pending/rejected.
        $validated['status'] = match ($venue->status) {
            'active', 'closed' => $request->boolean('temporarily_closed') ? 'closed' : 'active',
            default            => $venue->status, // pending/rejected: chờ admin
        };
        unset($validated['temporarily_closed']);

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

            // Ảnh upload qua form sửa cũng đặt làm ảnh bìa → hiển thị công khai
            // (home / tìm kiếm / trang chi tiết chỉ đọc venues.cover_image)
            $venue->update(['cover_image' => $filePath]);
        }

        return redirect()->route('owner.venues.show', $venue)->with('success', 'Cập nhật thông tin khu sân thành công!');
    }

    /**
     * Cập nhật giờ hoạt động theo tuần (7 ngày) — upsert từng ngày.
     * Khách đặt sân sẽ bị chặn theo giờ này, nên luôn ghi đè đủ 7 row.
     */
    public function updateOperatingHours(Request $request, Venue $venue)
    {
        $this->authorizeOwnership($venue);

        $validated = $request->validate([
            'hours'                 => 'required|array|size:7',
            'hours.*.is_closed'     => 'required|boolean',
            'hours.*.open_time'     => 'nullable|date_format:H:i',
            'hours.*.close_time'    => 'nullable|date_format:H:i|after:hours.*.open_time',
        ], [
            'hours.*.close_time.after' => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'hours.*.open_time.required' => 'Vui lòng nhập giờ mở cửa cho ngày mở bán.',
        ]);

        foreach ($validated['hours'] as $dayOfWeek => $hour) {
            if ($hour['is_closed']) {
                // Ngày nghỉ: xóa row cũ (nếu có) — không lưu giờ rác
                OperatingHour::where('venue_id', $venue->id)->where('day_of_week', $dayOfWeek)->delete();
                continue;
            }
            // Ngày mở: bắt buộc có đủ giờ mở/đóng
            if (empty($hour['open_time']) || empty($hour['close_time'])) {
                return back()->with('error', 'Ngày '.self::DAY_NAMES[$dayOfWeek].' chưa nhập đủ giờ mở/đóng cửa.');
            }
            OperatingHour::updateOrCreate(
                ['venue_id' => $venue->id, 'day_of_week' => $dayOfWeek],
                ['open_time' => $hour['open_time'], 'close_time' => $hour['close_time'], 'is_closed' => false]
            );
        }

        return redirect()->route('owner.venues.show', $venue)->with('success', 'Đã cập nhật giờ hoạt động!');
    }

    /**
     * Xóa khu sân (Sử dụng Soft Delete).
     */
    public function destroy(Venue $venue)
    {
        $this->authorizeOwnership($venue);

        // Xóa kèm tự động-cancel các đơn chưa diễn ra — 1 transaction, lỗi sẽ rollback toàn bộ
        $cancelled = $venue->deleteWithBookings();

        $msg = 'Đã xóa khu sân thành công!';
        if ($cancelled > 0) {
            $msg .= " Đã tự động hủy {$cancelled} đơn đặt sân chưa diễn ra.";
        }
        return redirect()->route('owner.venues.index')
            ->with('success', $msg);
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