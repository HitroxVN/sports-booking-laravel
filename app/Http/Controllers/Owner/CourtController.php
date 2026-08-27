<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourtController extends Controller
{
    /**
     * Hiển thị danh sách sân con của một khu sân.
     */
    public function index(Venue $venue)
    {
        $this->authorizeVenue($venue);

        $courts = $venue->courts()->with('sport')->latest()->paginate(10);
        return view('owner.courts.index', compact('venue', 'courts'));
    }

    /**
     * Form thêm sân con mới vào khu sân.
     */
    public function create(Venue $venue)
    {
        $this->authorizeVenue($venue);

        $sports = Sport::where('is_active', true)->get();
        return view('owner.courts.create', compact('venue', 'sports'));
    }

    /**
     * Lưu sân con mới.
     */
    public function store(Request $request, Venue $venue)
    {
        $this->authorizeVenue($venue);

        $validated = $request->validate([
            'sport_id'     => 'required|exists:sports,id',
            'name'         => 'required|string|max:255',
            'surface_type' => 'required|in:natural_grass,artificial_turf,wood,concrete',
            'max_players'  => 'nullable|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,maintenance,closed',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['venue_id'] = $venue->id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        Court::create($validated);

        return redirect()->route('owner.venues.courts.index', $venue->slug)
            ->with('success', 'Thêm sân con thành công!');
    }

    /**
     * Form chỉnh sửa sân con.
     */
    public function edit(Court $court)
    {
        $this->authorizeVenue($court->venue);

        $sports = Sport::where('is_active', true)->get();
        return view('owner.courts.edit', compact('court', 'sports'));
    }

    /**
     * Cập nhật thông tin sân con.
     */
    public function update(Request $request, Court $court)
    {
        $this->authorizeVenue($court->venue);

        $validated = $request->validate([
            'sport_id'     => 'required|exists:sports,id',
            'name'         => 'required|string|max:255',
            'surface_type' => 'required|in:natural_grass,artificial_turf,wood,concrete',
            'max_players'  => 'nullable|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,maintenance,closed',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($court->image && Storage::disk('public')->exists($court->image)) {
                Storage::disk('public')->delete($court->image);
            }

            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);

        return redirect()->route('owner.venues.courts.index', $court->venue->slug)
            ->with('success', 'Cập nhật sân con thành công!');
    }

    /**
     * Xóa sân con (Soft Delete).
     */
    public function destroy(Court $court)
    {
        $venue = $court->venue;
        $this->authorizeVenue($venue);

        $court->delete();

        return redirect()->route('owner.venues.courts.index', $venue->slug)
            ->with('success', 'Đã xóa sân con thành công!');
    }

    private function authorizeVenue(Venue $venue)
    {
        abort_if($venue->owner_id !== auth()->id(), 403, 'Bạn không có quyền thao tác trên khu sân này.');
    }
}