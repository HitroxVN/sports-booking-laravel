<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    // index — filter theo tab status (pending|active|rejected|deleted)
    public function index(Request $request)
    {
        $query = Venue::with('owner')->latest();
        if ($request->status === 'deleted') $query->onlyTrashed();
        elseif ($request->filled('status')) $query->where('status', $request->status);
        $venues       = $query->paginate(15)->withQueryString();
        $pendingCount = Venue::where('status', 'pending')->count();
        return view('admin.venues.index', compact('venues', 'pendingCount'));
    }

    // restore — khôi phục venue đã xóa mềm (trở lại trạng thái cũ, hiện diện lại trong tìm kiếm)
    public function restore(Venue $venue)
    {
        $venue->restore();
        return back()->with('success', "Đã khôi phục khu sân \"{$venue->name}\".");
    }

    // approve — chỉ duyệt venue đang pending
    public function approve(Venue $venue)
    {
        abort_if($venue->status !== 'pending', 403, 'Chỉ được duyệt khu sân đang chờ duyệt.');
        $venue->update(['status' => 'active', 'reject_reason' => null]);
        return back()->with('success', "Đã duyệt khu sân \"{$venue->name}\".");
    }

    // reject — chỉ từ chối venue đang pending
    public function reject(Request $request, Venue $venue)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        abort_if($venue->status !== 'pending', 403, 'Chỉ được từ chối khu sân đang chờ duyệt.');
        $venue->update(['status' => 'rejected', 'reject_reason' => $request->reason]);
        return back()->with('success', "Đã từ chối khu sân \"{$venue->name}\".");
    }

    // destroy — xóa mềm (kèm tự động hủy các đơn chưa diễn ra, chạy trong 1 transaction);
    // chỉ cho phép xóa venue đã duyệt — pending đi qua reject, rejected/closed không cần xóa
    public function destroy(Venue $venue)
    {
        abort_if($venue->status !== 'active', 403, 'Chỉ được xóa khu sân đã duyệt.');
        $cancelled = $venue->deleteWithBookings();

        $msg = "Đã xóa khu sân \"{$venue->name}\".";
        if ($cancelled > 0) {
            $msg .= " Đã tự động hủy {$cancelled} đơn đặt sân chưa diễn ra.";
        }
        return redirect()->route('admin.venues.index')->with('success', $msg);
    }
}
