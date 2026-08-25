<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách khu sân của chủ sân
        $venues = Venue::where('owner_id', auth()->id())->with('courts')->get();

        // Nếu chủ sân chưa có sân nào
        if ($venues->isEmpty()) {
            return view('owner.schedule.index', [
                'venues' => $venues,
                'courts' => collect(),
                'bookings' => collect(),
                'selectedDate' => today(),
                'selectedVenueId' => null,
            ]);
        }

        // Lấy khu sân đang chọn (mặc định lấy khu đầu tiên)
        $selectedVenueId = $request->get('venue_id', $venues->first()->id);
        $selectedVenue = $venues->firstWhere('id', $selectedVenueId) ?? $venues->first();
        
        // Lấy các sân con thuộc khu sân này
        $courts = $selectedVenue->courts;
        $courtIds = $courts->pluck('id');

        // Lấy ngày cần xem lịch (mặc định là hôm nay)
        $selectedDate = $request->filled('date') 
            ? Carbon::parse($request->date) 
            : today();

        // Lấy danh sách đơn đặt sân trong ngày đó của các sân con này
        $bookings = Booking::with(['user', 'court'])
            ->whereIn('court_id', $courtIds)
            ->whereDate('booking_date', $selectedDate)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->get();

        return view('owner.schedule.index', compact(
            'venues', 
            'selectedVenue', 
            'courts', 
            'bookings', 
            'selectedDate', 
            'selectedVenueId'
        ));
    }
}