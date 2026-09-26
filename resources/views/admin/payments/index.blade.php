<x-admin-layout :title="'Lịch sử thanh toán'">
    <div x-data="{
        showModal: false,
        paymentDetail: null,
        loading: false,
        openDetail(id) {
            this.loading = true;
            this.showModal = true;
            fetch('/admin/payments/' + id)
                .then(res => res.json())
                .then(data => {
                    this.paymentDetail = data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        }
    }">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Lịch sử thanh toán</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Theo dõi chi tiết các giao dịch thanh toán đơn hàng qua SePay, VNPay, MoMo và Tiền mặt
                </p>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Xem báo cáo tổng hợp
                </a>
            </div>
        </div>

        {{-- 4 Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card-base p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tổng thực thu</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                    {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }} đ
                </p>
                <p class="text-[11px] text-zinc-400 mt-1">Giao dịch đã xác nhận thành công</p>
            </div>

            <div class="card-base p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">GD Thành công</p>
                <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1">
                    {{ number_format($stats['total_successful'] ?? 0) }}
                </p>
                <p class="text-[11px] text-zinc-400 mt-1">Lượt giao dịch hoàn tất</p>
            </div>

            <div class="card-base p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tiền đã hoàn</p>
                <p class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">
                    {{ number_format($stats['total_refunded'] ?? 0, 0, ',', '.') }} đ
                </p>
                <p class="text-[11px] text-zinc-400 mt-1">Hủy đơn & hoàn cọc</p>
            </div>

            <div class="card-base p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Đang chờ xử lý</p>
                <p class="text-2xl font-extrabold text-amber-500 dark:text-amber-400 mt-1">
                    {{ number_format($stats['total_pending'] ?? 0) }}
                </p>
                <p class="text-[11px] text-zinc-400 mt-1">Cần đối soát hoặc chờ cổng</p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card-base p-5 mb-6">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="label-eyebrow block mb-1">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Mã đơn (BOOK-...), mã GD, tên khách..."
                           class="input-base">
                </div>

                <div>
                    <label class="label-eyebrow block mb-1">Cổng thanh toán</label>
                    <select name="gateway" class="input-base w-auto">
                        <option value="">Tất cả cổng</option>
                        <option value="sepay" @selected(request('gateway') === 'sepay')>SePay (Chuyển khoản)</option>
                        <option value="vnpay" @selected(request('gateway') === 'vnpay')>VNPay</option>
                        <option value="momo" @selected(request('gateway') === 'momo')>MoMo</option>
                        <option value="cash" @selected(request('gateway') === 'cash')>Tiền mặt tại sân</option>
                    </select>
                </div>

                <div>
                    <label class="label-eyebrow block mb-1">Trạng thái</label>
                    <select name="status" class="input-base w-auto">
                        <option value="">Tất cả trạng thái</option>
                        <option value="success" @selected(request('status') === 'success')>Thành công</option>
                        <option value="pending" @selected(request('status') === 'pending')>Đang chờ</option>
                        <option value="failed" @selected(request('status') === 'failed')>Thất bại</option>
                        <option value="refunded" @selected(request('status') === 'refunded')>Đã hoàn tiền</option>
                    </select>
                </div>

                <div>
                    <label class="label-eyebrow block mb-1">Loại giao dịch</label>
                    <select name="type" class="input-base w-auto">
                        <option value="">Tất cả loại</option>
                        <option value="deposit" @selected(request('type') === 'deposit')>Tiền đặt cọc</option>
                        <option value="full" @selected(request('type') === 'full')>Toàn bộ đơn</option>
                        <option value="refund" @selected(request('type') === 'refund')>Hoàn tiền</option>
                    </select>
                </div>

                <div>
                    <label class="label-eyebrow block mb-1">Từ ngày</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-base w-auto">
                </div>

                <div>
                    <label class="label-eyebrow block mb-1">Đến ngày</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-base w-auto">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-primary">Lọc</button>
                    @if(request()->hasAny(['search', 'gateway', 'status', 'type', 'from_date', 'to_date']))
                        <a href="{{ route('admin.payments.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Payment Records Table --}}
        <div class="card-base overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-100/70 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 uppercase tracking-wider font-semibold border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="py-3 px-4">Mã ĐH</th>
                            <th class="py-3 px-4">Khách hàng</th>
                            <th class="py-3 px-4">Số tiền</th>
                            <th class="py-3 px-4">Cổng thanh toán</th>
                            <th class="py-3 px-4">Loại GD</th>
                            <th class="py-3 px-4">Trạng thái</th>
                            <th class="py-3 px-4">Thời gian</th>
                            <th class="py-3 px-4">Mã GD Cổng</th>
                            <th class="py-3 px-4 text-right">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                                {{-- Mã đơn --}}
                                <td class="py-3 px-4">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span class="font-mono">{{ $payment->booking->code ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-[11px] text-zinc-400 dark:text-zinc-500">
                                        {{ $payment->booking?->court?->venue?->name ?? 'Khu sân' }}
                                    </div>
                                </td>

                                {{-- Khách hàng --}}
                                <td class="py-3 px-4">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $payment->booking?->user?->name ?? 'Khách vãng lai' }}
                                    </div>
                                    <div class="text-[11px] text-zinc-400">
                                        {{ $payment->booking?->user?->phone ?? $payment->booking?->user?->email ?? '-' }}
                                    </div>
                                </td>

                                {{-- Số tiền --}}
                                <td class="py-3 px-4 font-bold {{ $payment->type === 'refund' ? 'text-purple-600 dark:text-purple-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $payment->type === 'refund' ? '-' : '+' }}{{ number_format($payment->amount, 0, ',', '.') }} đ
                                </td>

                                {{-- Cổng thanh toán --}}
                                <td class="py-3 px-4">
                                    @php
                                        $gw = strtolower($payment->gateway);
                                    @endphp
                                    @if($gw === 'sepay')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            SePay QR
                                        </span>
                                    @elseif($gw === 'vnpay')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            VNPay
                                        </span>
                                    @elseif($gw === 'momo')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-pink-50 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                                            MoMo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                            Tiền mặt
                                        </span>
                                    @endif
                                </td>

                                {{-- Loại giao dịch --}}
                                <td class="py-3 px-4">
                                    @if($payment->type === 'deposit')
                                        <span class="text-zinc-700 dark:text-zinc-300 font-medium">Đặt cọc</span>
                                    @elseif($payment->type === 'full')
                                        <span class="text-zinc-700 dark:text-zinc-300 font-medium">Toàn phần</span>
                                    @elseif($payment->type === 'refund')
                                        <span class="text-purple-600 dark:text-purple-400 font-medium">Hoàn cọc</span>
                                    @else
                                        <span class="text-zinc-500">{{ $payment->type }}</span>
                                    @endif
                                </td>

                                {{-- Trạng thái --}}
                                <td class="py-3 px-4">
                                    @if($payment->status === 'success')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Thành công
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Đang chờ
                                        </span>
                                    @elseif($payment->status === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400 border border-red-200 dark:border-red-800">
                                            Thất bại
                                        </span>
                                    @elseif($payment->status === 'refunded')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            Đã hoàn
                                        </span>
                                    @endif
                                </td>

                                {{-- Thời gian --}}
                                <td class="py-3 px-4">
                                    <div class="text-zinc-800 dark:text-zinc-200">
                                        {{ $payment->paid_at ? $payment->paid_at->format('H:i d/m/Y') : $payment->created_at->format('H:i d/m/Y') }}
                                    </div>
                                    <div class="text-[11px] text-zinc-400">
                                        {{ ($payment->paid_at ?? $payment->created_at)->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Mã GD Cổng --}}
                                <td class="py-3 px-4 font-mono text-[11px] text-zinc-600 dark:text-zinc-400 truncate max-w-[150px]">
                                    {{ $payment->gateway_txn_id ?: '-' }}
                                </td>

                                {{-- Thao tác xem modal --}}
                                <td class="py-3 px-4 text-right">
                                    <button @click="openDetail({{ $payment->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition-colors">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    Không tìm thấy giao dịch thanh toán nào phù hợp với điều kiện lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($payments->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

        {{-- Detail Modal --}}
        <div x-show="showModal"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @keydown.escape.window="showModal = false">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl overflow-hidden text-zinc-900 dark:text-zinc-100"
                 @click.outside="showModal = false">
                
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <span>Chi tiết giao dịch thanh toán</span>
                    </h3>
                    <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="py-4 space-y-4 max-h-[75vh] overflow-y-auto">
                    <template x-if="loading">
                        <div class="py-12 flex justify-center items-center">
                            <svg class="animate-spin h-7 w-7 text-primary-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>

                    <template x-if="!loading && paymentDetail">
                        <div class="space-y-4 text-xs">
                            {{-- Info Grid --}}
                            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-700/60">
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Mã đơn hàng</span>
                                    <span class="font-mono font-bold text-zinc-900 dark:text-zinc-50 text-sm" x-text="paymentDetail.booking.code"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Số tiền giao dịch</span>
                                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm" x-text="paymentDetail.formatted_amount"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Cổng thanh toán</span>
                                    <span class="font-semibold uppercase" x-text="paymentDetail.gateway"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Trạng thái</span>
                                    <span class="font-semibold uppercase" x-text="paymentDetail.status"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Mã GD Cổng</span>
                                    <span class="font-mono text-zinc-700 dark:text-zinc-300" x-text="paymentDetail.gateway_txn_id || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-400 block mb-0.5">Thời gian thanh toán</span>
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300" x-text="paymentDetail.paid_at || paymentDetail.created_at"></span>
                                </div>
                            </div>

                            {{-- Booking & Court info --}}
                            <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-700/60 space-y-2">
                                <h4 class="font-bold text-zinc-800 dark:text-zinc-200">Thông tin Đơn đặt sân</h4>
                                <div class="grid grid-cols-2 gap-2 text-zinc-600 dark:text-zinc-400">
                                    <div>Khách hàng: <strong class="text-zinc-900 dark:text-zinc-100" x-text="paymentDetail.booking.customer_name"></strong></div>
                                    <div>Điện thoại: <span class="font-mono" x-text="paymentDetail.booking.customer_phone"></span></div>
                                    <div>Khu sân: <span x-text="paymentDetail.booking.venue_name"></span></div>
                                    <div>Sân con: <span x-text="paymentDetail.booking.court_name"></span></div>
                                    <div>Ngày đặt: <span x-text="paymentDetail.booking.booking_date"></span></div>
                                    <div>Giờ chơi: <span x-text="paymentDetail.booking.time_range"></span></div>
                                    <div>Tổng giá trị đơn: <span class="font-bold" x-text="paymentDetail.booking.total_amount"></span></div>
                                    <div>Tiền cọc quy định: <span x-text="paymentDetail.booking.deposit_amount"></span></div>
                                </div>
                            </div>

                            {{-- Raw Gateway Response JSON --}}
                            <template x-if="paymentDetail.gateway_response">
                                <div class="p-3.5 rounded-xl bg-zinc-950 text-zinc-300 font-mono text-[11px] overflow-x-auto max-h-48 border border-zinc-800">
                                    <span class="text-zinc-500 block mb-1 font-sans font-semibold">Gateway Raw Response:</span>
                                    <pre x-text="JSON.stringify(paymentDetail.gateway_response, null, 2)"></pre>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                    <button @click="showModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
