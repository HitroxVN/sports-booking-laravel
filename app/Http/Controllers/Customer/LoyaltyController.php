<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Reward;
use App\Services\LoyaltyService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function redeem(Request $request, Reward $reward, LoyaltyService $loyaltyService): RedirectResponse
    {
        try {
            $voucher = $loyaltyService->redeem($request->user(), $reward);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            'success',
            "Đổi thưởng thành công! Voucher của bạn là {$voucher->voucher_code}."
        );
    }

    public function apply(Request $request, Booking $booking, LoyaltyService $loyaltyService): RedirectResponse
    {
        $validated = $request->validate([
            'loyalty_transaction_id' => ['required', 'integer'],
        ]);

        $voucher = $request->user()
            ->loyaltyTransactions()
            ->findOrFail($validated['loyalty_transaction_id']);

        try {
            $loyaltyService->applyVoucher($booking, $voucher, $request->user());
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('customer.bookings.pay', $booking)
            ->with('success', 'Đã áp dụng voucher điểm thưởng vào đơn đặt sân.');
    }
}
