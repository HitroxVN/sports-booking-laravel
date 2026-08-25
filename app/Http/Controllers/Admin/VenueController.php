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

    // approve
    public function approve($id)
    {
        $venue = Venue::findOrFail($id); // findOrFail($id) vì route key là slug
        $venue->update(['status' => 'active', 'reject_reason' => null]);
        return back()->with('success', "Đã duyệt khu sân \"{$venue->name}\".");
    }

    // reject — lưu lý do
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $venue = Venue::findOrFail($id);
        $venue->update(['status' => 'rejected', 'reject_reason' => $request->reason]);
        return back()->with('success', "Đã từ chối khu sân \"{$venue->name}\".");
    }
}
