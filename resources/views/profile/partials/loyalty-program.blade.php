@php
    $tierLabels = [
        'bronze' => 'Đồng',
        'silver' => 'Bạc',
        'gold' => 'Vàng',
        'platinum' => 'Bạch kim',
    ];
    $tierStyles = [
        'bronze' => 'from-amber-700 to-orange-500',
        'silver' => 'from-slate-500 to-slate-300',
        'gold' => 'from-amber-500 to-yellow-300',
        'platinum' => 'from-cyan-600 to-indigo-500',
    ];
@endphp

<section class="mb-8 space-y-6" aria-labelledby="loyalty-heading">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="rounded-2xl p-6 text-white bg-gradient-to-br {{ $tierStyles[$user->tier] ?? $tierStyles['bronze'] }} shadow-lg">
            <p class="text-sm font-semibold text-white/80">Điểm Arena hiện có</p>
            <div class="mt-2 flex items-end justify-between gap-4">
                <p class="text-4xl font-black tracking-tight">{{ number_format($user->points) }}</p>
                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                    Hạng {{ $tierLabels[$user->tier] ?? 'Đồng' }}
                </span>
            </div>
            <p class="mt-5 text-xs text-white/80">Mỗi 10.000đ thanh toán thành công = 1 điểm.</p>
        </div>

        <div class="card-base p-6 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="loyalty-heading" class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Đổi điểm lấy voucher</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Voucher đã đổi có thể áp dụng tại trang thanh toán đặt sân.</p>
                </div>
                <span class="rounded-full bg-primary-50 dark:bg-primary-900/30 px-3 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300">
                    Bạc 100 · Vàng 500 · Bạch kim 1.000 điểm
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($rewards as $reward)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 flex flex-col">
                        <h3 class="font-bold text-zinc-900 dark:text-zinc-100">{{ $reward->name }}</h3>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 flex-1">
                            {{ $reward->description ?: 'Dùng cho một đơn đặt sân hợp lệ.' }}
                        </p>
                        @if($reward->min_amount)
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Đơn tối thiểu {{ number_format($reward->min_amount) }}đ</p>
                        @endif
                        <form method="POST" action="{{ route('customer.loyalty.rewards.redeem', $reward) }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                    @disabled($user->points < $reward->points_required)
                                    class="w-full rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $user->points >= $reward->points_required ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 cursor-not-allowed' }}">
                                Đổi {{ number_format($reward->points_required) }} điểm
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="sm:col-span-2 xl:col-span-3 text-sm text-zinc-500 dark:text-zinc-400">Chưa có phần thưởng nào đang mở.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if($availableVouchers->isNotEmpty())
        <div class="card-base p-6">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Voucher chưa sử dụng</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($availableVouchers as $voucher)
                    <div class="rounded-xl border border-dashed border-primary-300 dark:border-primary-700 bg-primary-50/60 dark:bg-primary-900/10 p-4">
                        <p class="font-mono font-bold text-primary-700 dark:text-primary-300">{{ $voucher->voucher_code }}</p>
                        <p class="mt-1 text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $voucher->reward?->name }}</p>
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Hết hạn {{ $voucher->expires_at?->format('d/m/Y H:i') ?? 'không giới hạn' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card-base overflow-hidden">
        <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Lịch sử điểm thưởng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-left text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Thời gian</th>
                        <th class="px-6 py-3 font-semibold">Nội dung</th>
                        <th class="px-6 py-3 font-semibold text-right">Điểm</th>
                        <th class="px-6 py-3 font-semibold text-right">Số dư</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($loyaltyTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-zinc-800 dark:text-zinc-200">
                                {{ $transaction->description }}
                                @if($transaction->booking)
                                    <span class="block mt-0.5 text-xs text-zinc-400">Đơn {{ $transaction->booking->code }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $transaction->points >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $transaction->points >= 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($transaction->balance_after) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Bạn chưa có giao dịch điểm thưởng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($loyaltyTransactions->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $loyaltyTransactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</section>
