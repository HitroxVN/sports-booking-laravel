<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Nhận webhook giao dịch ngân hàng từ SePay (server-to-server).
 *
 * Chiến lược mã lỗi: 401 sai key / 400 thiếu dữ liệu / 422 thiếu tiền (SePay sẽ retry
 * vì khách có thể chuyển bổ sung). Mọi trường hợp không thể xử lý vĩnh viễn (mã đơn
 * không tồn tại, đơn đã hủy...) trả 200 để SePay không retry vô hạn.
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

        // 4. Xử lý trong transaction để idempotency + khóa dòng booking không bị race
        $result = DB::transaction(function () use ($data) {
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

            // 4c. Đối chiếu số tiền: đủ tổng -> xác nhận đơn; đủ cọc -> đánh dấu đã cọc
            $amount  = (float) $data['transferAmount'];
            $total   = (float) $booking->total_amount;
            $deposit = $booking->deposit_amount !== null ? (float) $booking->deposit_amount : null;

            if ($amount >= $total) {
                [$type, $paymentStatus, $confirm] = ['full', 'fully_paid', true];
            } elseif ($deposit !== null && $amount >= $deposit) {
                [$type, $paymentStatus, $confirm] = ['deposit', 'deposit_paid', false];
            } else {
                // 422 để SePay retry — khách có thể chuyển khoản bổ sung sau
                return [422, ['success' => false, 'message' => 'Insufficient amount']];
            }

            // 4d. Lưu giao dịch + cập nhật đơn
            Payment::create([
                'booking_id'      => $booking->id,
                'gateway'         => 'sepay',
                'gateway_txn_id'  => (string) $data['id'],
                'amount'          => $amount,
                'type'            => $type,
                'status'          => 'success',
                'gateway_response'=> $data,
                'paid_at'         => $data['transactionDate'],
            ]);

            $booking->payment_status = $paymentStatus;
            if ($confirm && $booking->isPending()) {
                $booking->status = 'confirmed'; // chỉ pending -> confirmed, không đụng đơn đã hủy/hoàn thành
            }
            $booking->save();

            return [200, ['success' => true, 'message' => 'Payment verified']];
        });

        return response()->json($result[1], $result[0]);
    }
}
