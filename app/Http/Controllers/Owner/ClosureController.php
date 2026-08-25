<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\CourtClosure;
use Illuminate\Http\Request;

class ClosureController extends Controller
{
    public function index(Court $court)
    {
        $this->authorizeCourt($court);

        $closures = $court->closures()
            ->orderBy('date', 'desc')
            ->orderBy('start_time')
            ->paginate(15);

        return view('owner.closures.index', compact('court', 'closures'));
    }

    public function create(Court $court)
    {
        $this->authorizeCourt($court);
        return view('owner.closures.create', compact('court'));
    }

    public function store(Request $request, Court $court)
    {
        $this->authorizeCourt($court);

        $validated = $request->validate([
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
            'reason'     => 'required|string|max:255',
        ]);

        $court->closures()->create($validated);

        return redirect()->route('owner.courts.closures.index', $court)
            ->with('success', 'Đã thêm lịch khóa sân thành công!');
    }

    public function destroy(CourtClosure $closure)
    {
        $court = $closure->court;
        $this->authorizeCourt($court);

        $closure->delete();

        return redirect()->route('owner.courts.closures.index', $court)
            ->with('success', 'Đã gỡ bỏ lịch khóa sân!');
    }

    private function authorizeCourt(Court $court)
    {
        abort_if(
            $court->venue->owner_id !== auth()->id(), 
            403, 
            'Bạn không có quyền thao tác trên sân này.'
        );
    }
}