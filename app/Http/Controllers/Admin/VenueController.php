<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    // index — filter theo tab status
    public function index(Request $request)
    {
        $query = Venue::with('owner')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $venues       = $query->paginate(15)->withQueryString();
        $pendingCount = Venue::where('status', 'pending')->count();
        return view('admin.venues.index', compact('venues', 'pendingCount'));
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
}
