<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Danh sách lịch sử thanh toán của tất cả đơn hàng
     */
    public function index(Request $request)
    {
        $query = Payment::with([
            'booking.user',
            'booking.court.venue',
        ])->latest('id');

        // 1. Tìm kiếm theo mã đơn, mã GD, hoặc thông tin khách
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('gateway_txn_id', 'like', "%{$search}%")
                  ->orWhereHas('booking', function ($bq) use ($search) {
                      $bq->where('code', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($uq) use ($search) {
                             $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                         });
                  });
            });
        }

        // 2. Lọc theo cổng thanh toán
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // 3. Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Lọc theo loại thanh toán (cọc, toàn bộ, hoàn tiền)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 5. Lọc theo khoảng ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->from_date));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date));
        }

        // Thống kê nhanh trên toàn bộ hệ thống
        $stats = [
            'total_revenue'    => Payment::where('status', 'success')->where('type', '!=', 'refund')->sum('amount'),
            'total_refunded'   => Payment::where('status', 'refunded')->orWhere('type', 'refund')->sum('amount'),
            'total_successful' => Payment::where('status', 'success')->count(),
            'total_pending'    => Payment::where('status', 'pending')->count(),
        ];

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Chi tiết 1 giao dịch thanh toán (cho modal popup)
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load([
            'booking.user',
            'booking.court.venue',
        ]);

        return response()->json([
            'id'               => $payment->id,
            'gateway'          => $payment->gateway,
            'gateway_txn_id'   => $payment->gateway_txn_id,
            'amount'           => (float) $payment->amount,
            'formatted_amount' => number_format($payment->amount, 0, ',', '.') . ' đ',
            'type'             => $payment->type,
            'status'           => $payment->status,
            'gateway_response' => $payment->gateway_response,
            'paid_at'          => $payment->paid_at ? $payment->paid_at->format('H:i d/m/Y') : null,
            'created_at'       => $payment->created_at->format('H:i d/m/Y'),
            'booking'          => [
                'id'             => $payment->booking->id ?? null,
                'code'           => $payment->booking->code ?? 'N/A',
                'booking_date'   => $payment->booking?->booking_date?->format('d/m/Y'),
                'time_range'     => substr($payment->booking?->start_time ?? '', 0, 5) . ' - ' . substr($payment->booking?->end_time ?? '', 0, 5),
                'total_amount'   => number_format($payment->booking?->total_amount ?? 0, 0, ',', '.') . ' đ',
                'deposit_amount' => number_format($payment->booking?->deposit_amount ?? 0, 0, ',', '.') . ' đ',
                'court_name'     => $payment->booking?->court?->name ?? 'N/A',
                'venue_name'     => $payment->booking?->court?->venue?->name ?? 'N/A',
                'customer_name'  => $payment->booking?->user?->name ?? 'Khách vãng lai',
                'customer_email' => $payment->booking?->user?->email ?? '-',
                'customer_phone' => $payment->booking?->user?->phone ?? '-',
            ],
        ]);
    }
}
