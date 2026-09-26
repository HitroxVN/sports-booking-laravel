<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmed;
use App\Notifications\PaymentReceived;
use App\Services\Notifier;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Nhận webhook giao dịch ngân hàng từ SePay (server-to-server).
 *
 * Chiến lược mã lỗi: 401 sai key / 400 thiếu dữ liệu. Mọi trường hợp không thể xử lý
 * vĩnh viễn (mã đơn không tồn tại, đơn đã hủy, chưa đủ cọc...) trả 200 để SePay
 * không retry vô hạn — tiền chưa đủ vẫn được lưu payment pending để cộng dồn lần sau.
 */
class SePayWebhookController extends Controller
{
    public function invoke(Request $request): JsonResponse
    {
        // 1. Xác thực: SePay gửi "Authorization: Bearer {key}" hoặc "Apikey {key}"
        // tùy Token Type cấu hình trong dashboard SePay
        $token = preg_replace('/^(Bearer|Apikey)\s+/i', '', $request->header('Authorization', ''));

        if (!hash_equals((string) config('services.sepay.webhook_key'), $token)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all();

        // 2. Kiểm tra dữ liệu bắt buộc
        foreach (['id', 'content', 'transferAmount', 'transactionDate', 'transferType'] as $field) {
            if (!isset($data[$field])) {
                return response()->json(['success' => false, 'message' => "Missing field: {$field}"], 400);
            }
        }

        // 3. Chỉ xử lý tiền vào (bỏ qua giao dịch tiền ra)
        if ($data['transferType'] !== 'in') {
            return response()->json(['success' => true, 'message' => 'Ignored (transferType out)']);
        }

        // Gom dữ liệu cần cho thông báo để gửi SAU khi transaction commit ($notify = null nghĩa là không có gì để báo)
        $notify = null;

        // 4. Xử lý trong transaction để idempotency + khóa dòng booking không bị race
        $result = DB::transaction(function () use ($data, &$notify) {
            // 4a. Chống xử lý trùng webhook (SePay có thể gửi lại cùng giao dịch)
            $alreadyProcessed = Payment::where('gateway', 'sepay')
                ->where('gateway_txn_id', (string) $data['id'])
                ->exists();

            if ($alreadyProcessed) {
                return [200, ['success' => true, 'message' => 'Already processed']];
            }

            // 4b. Tách mã đơn (BKXXXXXXXX) khỏi nội dung chuyển khoản.
            // Mã mới không chứa "-", nhưng vẫn bỏ qua dấu gạch/khoảng trắng nếu ngân hàng chèn thêm
            if (!preg_match('/BK[\s\-]*([A-Z0-9]{8})/', strtoupper($data['content']), $matches)) {
                return [200, ['success' => false, 'message' => 'No booking code in content']];
            }

            // Đơn cũ có mã "BK-XXXXXXXX", đơn mới "BKXXXXXXXX" — thử cả hai
            $booking = Booking::whereIn('code', ['BK' . $matches[1], 'BK-' . $matches[1]])
                ->lockForUpdate()->first();

            if (!$booking) {
                return [200, ['success' => false, 'message' => 'Booking not found']];
            }

            if ($booking->isCancelled() || $booking->isCompleted()) {
                return [200, ['success' => false, 'message' => "Booking is {$booking->status}; handle refund manually"]];
            }

            // 4c. Đối chiếu số tiền CỘNG DỒN: giao dịch này + các giao dịch thành công trước đó
            // so với tổng tiền / tiền cọc. Khách thường bị hạn mức chuyển khoản nên chia nhiều lần.
            $amount    = (float) $data['transferAmount'];
            $total     = (float) $booking->total_amount;
            $deposit   = $booking->deposit_amount !== null ? (float) $booking->deposit_amount : null;
            $paidSoFar = (float) Payment::where('booking_id', $booking->id)
                ->where('status', 'success')
                ->where('type', '!=', 'refund')
                ->sum('amount');

            if ($amount + $paidSoFar >= $total) {
                [$type, $paymentStatus, $confirm] = ['full', 'fully_paid', true];
            } elseif ($deposit !== null && $amount + $paidSoFar >= $deposit) {
                [$type, $paymentStatus, $confirm] = ['deposit', 'deposit_paid', false];
            } else {
                // Chưa đủ cọc: vẫn lưu giao dịch (status pending) để admin thấy tiền đã vào,
                // giao dịch kế tiếp sẽ được cộng dồn. Trả 200 để SePay không retry vô hạn.
                [$type, $paymentStatus, $confirm] = ['deposit', 'pending', false];
            }

            // 4d. Lưu giao dịch + cập nhật đơn
            // gateway_txn_id có unique index (gateway, gateway_txn_id) — nếu 2 webhook trùng
            // đến song song, bản ghi thứ 2 văng exception thay vì tạo payment đếm đôi.
            try {
                Payment::create([
                    'booking_id'      => $booking->id,
                    'gateway'         => 'sepay',
                    'gateway_txn_id'  => (string) $data['id'],
                    'amount'          => $amount,
                    'type'            => $type,
                    'status'          => $paymentStatus === 'pending' ? 'pending' : 'success',
                    'gateway_response'=> $data,
                    'paid_at'         => $data['transactionDate'],
                ]);
            } catch (UniqueConstraintViolationException) {
                return [200, ['success' => true, 'message' => 'Already processed']];
            }

            // Chốt sân lần này hay chỉ là đơn trước đó đã confirmed (đọc trước khi đổi status)
            $newlyConfirmed = $confirm && $booking->isPending();

            $booking->payment_status = $paymentStatus;
            if ($newlyConfirmed) {
                $booking->status = 'confirmed'; // chỉ pending -> confirmed, không đụng đơn đã hủy/hoàn thành
            }
            $booking->save();

            $notify = [
                'booking'        => $booking,
                'amount'         => $amount,
                'fullyPaid'      => $paymentStatus === 'fully_paid',
                'newlyConfirmed' => $newlyConfirmed,
            ];

            return [200, ['success' => true, 'message' => 'Payment verified']];
        });

        // 5. Thông báo cho khách (ngoài transaction — chỉ chạy khi đã commit thành công)
        if ($notify) {
            $booking = $notify['booking'];
            $booking->load(['user', 'court.venue']);

            Notifier::send($booking->user, new PaymentReceived($booking, $notify['amount'], $notify['fullyPaid']));

            if ($notify['newlyConfirmed']) {
                Notifier::send($booking->user, new BookingConfirmed($booking));
            }
        }

        return response()->json($result[1], $result[0]);
    }
}
