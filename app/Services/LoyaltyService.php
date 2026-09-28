<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\LoyaltyTransaction;
use App\Models\Reward;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoyaltyService
{
    public const VND_PER_POINT = 10_000;

    /**
     * Hạng dựa trên tổng điểm từng tích lũy, không giảm khi khách đổi thưởng.
     *
     * @var array<string, int>
     */
    public const TIER_THRESHOLDS = [
        'platinum' => 1000,
        'gold' => 500,
        'silver' => 100,
        'bronze' => 0,
    ];

    public function awardForPaidBooking(Booking $booking): int
    {
        return DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()->lockForUpdate()->find($booking->getKey());

            if (! $lockedBooking || $lockedBooking->payment_status !== 'fully_paid') {
                return 0;
            }

            $reference = "booking:{$lockedBooking->id}:earn";

            if (LoyaltyTransaction::where('reference', $reference)->exists()) {
                return 0;
            }

            $user = User::query()->lockForUpdate()->find($lockedBooking->user_id);

            if (! $user?->isCustomer()) {
                return 0;
            }

            $points = (int) floor((float) $lockedBooking->total_amount / self::VND_PER_POINT);

            if ($points < 1) {
                return 0;
            }

            $balanceAfter = $user->points + $points;
            $lifetimePoints = (int) LoyaltyTransaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'earn')
                ->sum('points') + $points;

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $lockedBooking->id,
                'type' => 'earn',
                'points' => $points,
                'balance_after' => $balanceAfter,
                'description' => "Tích điểm từ đơn #{$lockedBooking->code}",
                'reference' => $reference,
            ]);

            $user->forceFill([
                'points' => $balanceAfter,
                'tier' => $this->tierFor($lifetimePoints),
            ])->save();

            return $points;
        });
    }

    public function redeem(User $user, Reward $reward): LoyaltyTransaction
    {
        return DB::transaction(function () use ($user, $reward) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $lockedReward = Reward::query()->lockForUpdate()->findOrFail($reward->getKey());

            if (! $lockedUser->isCustomer()) {
                throw new DomainException('Chỉ tài khoản khách hàng mới có thể đổi thưởng.');
            }

            if (! $lockedReward->is_active) {
                throw new DomainException('Phần thưởng này hiện không còn khả dụng.');
            }

            if ($lockedUser->points < $lockedReward->points_required) {
                throw new DomainException('Bạn không đủ điểm để đổi phần thưởng này.');
            }

            $balanceAfter = $lockedUser->points - $lockedReward->points_required;

            $voucher = LoyaltyTransaction::create([
                'user_id' => $lockedUser->id,
                'reward_id' => $lockedReward->id,
                'type' => 'redeem',
                'points' => -$lockedReward->points_required,
                'balance_after' => $balanceAfter,
                'description' => "Đổi voucher: {$lockedReward->name}",
                'reference' => 'reward:'.Str::uuid(),
                'voucher_code' => $this->generateVoucherCode(),
                'expires_at' => now()->addDays($lockedReward->valid_days),
            ]);

            $lockedUser->forceFill(['points' => $balanceAfter])->save();

            return $voucher;
        });
    }

    public function applyVoucher(Booking $booking, LoyaltyTransaction $voucher, User $user): Booking
    {
        return DB::transaction(function () use ($booking, $voucher, $user) {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->getKey());

            if ($lockedBooking->user_id !== $user->id) {
                throw new DomainException('Bạn không có quyền áp dụng voucher cho đơn này.');
            }

            if (! $lockedBooking->isPending() || $lockedBooking->payment_status !== 'unpaid') {
                throw new DomainException('Voucher chỉ áp dụng cho đơn đang chờ thanh toán.');
            }

            if ($lockedBooking->isPaymentExpired()) {
                throw new DomainException('Đơn đã hết thời gian thanh toán.');
            }

            if ($lockedBooking->promotion_id || $lockedBooking->loyalty_transaction_id) {
                throw new DomainException('Mỗi đơn chỉ được áp dụng một mã giảm giá hoặc voucher điểm thưởng.');
            }

            if ($lockedBooking->payments()->whereIn('status', ['pending', 'success'])->exists()) {
                throw new DomainException('Không thể đổi giá trị đơn sau khi đã phát sinh giao dịch thanh toán.');
            }

            $lockedVoucher = LoyaltyTransaction::query()
                ->with('reward')
                ->lockForUpdate()
                ->findOrFail($voucher->getKey());

            if ($lockedVoucher->user_id !== $user->id || ! $lockedVoucher->isAvailableVoucher()) {
                throw new DomainException('Voucher không hợp lệ, đã sử dụng hoặc đã hết hạn.');
            }

            $reward = $lockedVoucher->reward;

            if (! $reward) {
                throw new DomainException('Phần thưởng của voucher không còn tồn tại.');
            }

            $currentAmount = (float) $lockedBooking->total_amount;

            if ($reward->min_amount !== null && $currentAmount < (float) $reward->min_amount) {
                throw new DomainException('Đơn chưa đạt giá trị tối thiểu để áp dụng voucher này.');
            }

            $discount = $reward->discountFor($currentAmount);

            if ($discount <= 0) {
                throw new DomainException('Voucher không tạo ra giá trị giảm hợp lệ.');
            }

            $newTotal = max(0, $currentAmount - $discount);

            $bookingChanges = [
                'loyalty_transaction_id' => $lockedVoucher->id,
                'discount_amount' => (float) $lockedBooking->discount_amount + $discount,
                'total_amount' => $newTotal,
                'deposit_amount' => $lockedBooking->deposit_amount !== null
                    ? min((float) $lockedBooking->deposit_amount, $newTotal)
                    : null,
            ];

            if ($newTotal <= 0) {
                $bookingChanges['payment_status'] = 'fully_paid';
                $bookingChanges['status'] = 'confirmed';
            }

            $lockedBooking->forceFill($bookingChanges)->save();

            $lockedVoucher->forceFill([
                'booking_id' => $lockedBooking->id,
                'used_at' => now(),
            ])->save();

            return $lockedBooking;
        });
    }

    public function releaseVoucherFromCancelledBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()->lockForUpdate()->find($booking->getKey());

            if (! $lockedBooking
                || ! $lockedBooking->isCancelled()
                || $lockedBooking->payment_status !== 'unpaid'
                || ! $lockedBooking->loyalty_transaction_id) {
                return;
            }

            $voucher = LoyaltyTransaction::query()
                ->lockForUpdate()
                ->find($lockedBooking->loyalty_transaction_id);

            $lockedBooking->forceFill([
                'total_amount' => (float) $lockedBooking->total_amount + (float) $lockedBooking->discount_amount,
                'discount_amount' => 0,
                'loyalty_transaction_id' => null,
            ])->save();

            if ($voucher) {
                $voucher->forceFill([
                    'booking_id' => null,
                    'used_at' => null,
                ])->save();
            }
        });
    }

    public function tierFor(int $lifetimePoints): string
    {
        foreach (self::TIER_THRESHOLDS as $tier => $minimumPoints) {
            if ($lifetimePoints >= $minimumPoints) {
                return $tier;
            }
        }

        return 'bronze';
    }

    private function generateVoucherCode(): string
    {
        do {
            $code = 'ARENA-'.Str::upper(Str::random(10));
        } while (LoyaltyTransaction::where('voucher_code', $code)->exists());

        return $code;
    }
}
