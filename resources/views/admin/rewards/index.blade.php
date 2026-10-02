<x-admin-layout :title="'Quản lý phần thưởng đổi điểm'">

    <div x-data="{
        createModalOpen: false,
        editModalOpen: false,
        editForm: {
            id: null,
            name: '',
            description: '',
            points_required: 50,
            discount_type: 'fixed',
            discount_value: 20000,
            min_amount: '',
            max_discount: '',
            valid_days: 30,
            is_active: true,
            actionUrl: ''
        },
        openEdit(reward) {
            this.editForm = {
                id: reward.id,
                name: reward.name,
                description: reward.description || '',
                points_required: reward.points_required,
                discount_type: reward.discount_type,
                discount_value: reward.discount_value,
                min_amount: reward.min_amount || '',
                max_discount: reward.max_discount || '',
                valid_days: reward.valid_days,
                is_active: Boolean(reward.is_active),
                actionUrl: '/admin/rewards/' + reward.id
            };
            this.editModalOpen = true;
        }
    }">

        {{-- Page header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Phần thưởng đổi điểm</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cấu hình danh mục voucher khách hàng có thể đổi từ điểm tích luỹ Arena.</p>
            </div>
            <button type="button" @click="createModalOpen = true" class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm phần thưởng
            </button>
        </div>

        {{-- Thống kê nhanh --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tổng phần thưởng</p>
                <p class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 mt-2">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Đang hoạt động</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ number_format($stats['active']) }}</p>
            </div>
            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tạm dừng</p>
                <p class="text-2xl font-extrabold text-zinc-600 dark:text-zinc-400 mt-2">{{ number_format($stats['inactive']) }}</p>
            </div>
            <div class="card-base p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">Tổng lượt đổi</p>
                <p class="text-2xl font-extrabold text-primary-600 dark:text-primary-400 mt-2">{{ number_format($stats['total_redeemed']) }}</p>
            </div>
        </div>

        {{-- Filter tabs --}}
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('admin.rewards.index') }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($status) ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50' }}">
                Tất cả ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.rewards.index', ['status' => 'active']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'active' ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50' }}">
                Đang hoạt động ({{ $stats['active'] }})
            </a>
            <a href="{{ route('admin.rewards.index', ['status' => 'inactive']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'inactive' ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50' }}">
                Tạm dừng ({{ $stats['inactive'] }})
            </a>
        </div>

        {{-- Table --}}
        <div class="card-base overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Tên phần thưởng</th>
                            <th class="p-4 font-semibold text-center">Điểm cần</th>
                            <th class="p-4 font-semibold">Giá trị giảm</th>
                            <th class="p-4 font-semibold">Điều kiện áp dụng</th>
                            <th class="p-4 font-semibold text-center">Hiệu lực</th>
                            <th class="p-4 font-semibold text-center">Đã đổi</th>
                            <th class="p-4 font-semibold text-center">Trạng thái</th>
                            <th class="p-4 font-semibold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($rewards as $reward)
                        <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">
                            <td class="p-4">
                                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ $reward->name }}</p>
                                @if($reward->description)
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 max-w-xs truncate">{{ $reward->description }}</p>
                                @endif
                                <span class="font-mono text-[11px] text-zinc-400 dark:text-zinc-500">slug: {{ $reward->slug }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                    {{ number_format($reward->points_required) }} điểm
                                </span>
                            </td>
                            <td class="p-4">
                                @if($reward->discount_type === 'percent')
                                    <span class="font-bold text-primary-600 dark:text-primary-400">Giảm {{ (float)$reward->discount_value }}%</span>
                                    @if($reward->max_discount)
                                        <p class="text-xs text-zinc-500 mt-0.5">Tối đa {{ number_format($reward->max_discount) }}đ</p>
                                    @endif
                                @else
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Giảm {{ number_format($reward->discount_value) }}đ</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-zinc-600 dark:text-zinc-300">
                                @if($reward->min_amount)
                                    Đơn từ <strong>{{ number_format($reward->min_amount) }}đ</strong>
                                @else
                                    <span class="text-zinc-400">Không yêu cầu tối thiểu</span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $reward->valid_days }} ngày
                            </td>
                            <td class="p-4 text-center font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($reward->transactions_count) }}
                            </td>
                            <td class="p-4 text-center">
                                @if($reward->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300">
                                        ● Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400">
                                        ○ Tạm dừng
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                            @click="openEdit({{ json_encode($reward) }})"
                                            class="px-2.5 py-1.5 text-xs font-medium rounded-lg text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-950/50 transition-colors">
                                        Sửa
                                    </button>
                                    <form method="POST" action="{{ route('admin.rewards.destroy', $reward) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hoặc tắt phần thưởng này?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 text-xs font-medium rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors">
                                            Xóa/Tắt
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                Không tìm thấy phần thưởng nào. Hãy bấm <strong>+ Thêm phần thưởng</strong> để tạo mới.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rewards->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $rewards->links() }}
                </div>
            @endif
        </div>

        {{-- ==============================================================
             MODAL: TẠO PHẦN THƯỞNG MỚI
        =============================================================== --}}
        <div x-cloak x-show="createModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModalOpen" x-transition.opacity @click="createModalOpen = false" class="fixed inset-0 bg-black/60 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Thêm phần thưởng đổi điểm mới</h3>
                        <button type="button" @click="createModalOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">✕</button>
                    </div>

                    <form method="POST" action="{{ route('admin.rewards.store') }}" class="mt-4 space-y-4" x-data="{ discountType: 'fixed' }">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Tên phần thưởng <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="VD: Voucher giảm 30.000đ" class="input-base w-full">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Mô tả ngắn</label>
                            <input type="text" name="description" placeholder="VD: Áp dụng cho đơn đặt sân bất kỳ" class="input-base w-full">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Điểm yêu cầu <span class="text-red-500">*</span></label>
                                <input type="number" name="points_required" min="1" value="50" required class="input-base w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Thời hạn sử dụng (ngày) <span class="text-red-500">*</span></label>
                                <input type="number" name="valid_days" min="1" max="365" value="30" required class="input-base w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Loại giảm giá <span class="text-red-500">*</span></label>
                                <select name="discount_type" x-model="discountType" class="input-base w-full">
                                    <option value="fixed">Số tiền cố định (VNĐ)</option>
                                    <option value="percent">Phần trăm (%)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                    Mức giảm (<span x-text="discountType === 'percent' ? '%' : 'VNĐ'"></span>) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="discount_value" min="1" :max="discountType === 'percent' ? 100 : null" required class="input-base w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Đơn tối thiểu (VNĐ)</label>
                                <input type="number" name="min_amount" min="0" placeholder="0 nếu không yêu cầu" class="input-base w-full">
                            </div>
                            <div x-show="discountType === 'percent'">
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Giảm tối đa (VNĐ)</label>
                                <input type="number" name="max_discount" min="0" placeholder="Để trống nếu không giới hạn" class="input-base w-full">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" id="create_is_active" name="is_active" value="1" checked class="rounded border-zinc-300 text-primary-600 focus:ring-primary-500">
                            <label for="create_is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kích hoạt ngay (cho phép khách đổi điểm)</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" @click="createModalOpen = false" class="btn-secondary">Hủy</button>
                            <button type="submit" class="btn-primary">Tạo phần thưởng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==============================================================
             MODAL: CHỈNH SỬA PHẦN THƯỞNG
        =============================================================== --}}
        <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition.opacity @click="editModalOpen = false" class="fixed inset-0 bg-black/60 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Cập nhật phần thưởng</h3>
                        <button type="button" @click="editModalOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">✕</button>
                    </div>

                    <form method="POST" :action="editForm.actionUrl" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Tên phần thưởng <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editForm.name" required class="input-base w-full">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Mô tả ngắn</label>
                            <input type="text" name="description" x-model="editForm.description" class="input-base w-full">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Điểm yêu cầu <span class="text-red-500">*</span></label>
                                <input type="number" name="points_required" min="1" x-model="editForm.points_required" required class="input-base w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Thời hạn sử dụng (ngày) <span class="text-red-500">*</span></label>
                                <input type="number" name="valid_days" min="1" max="365" x-model="editForm.valid_days" required class="input-base w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Loại giảm giá <span class="text-red-500">*</span></label>
                                <select name="discount_type" x-model="editForm.discount_type" class="input-base w-full">
                                    <option value="fixed">Số tiền cố định (VNĐ)</option>
                                    <option value="percent">Phần trăm (%)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                    Mức giảm (<span x-text="editForm.discount_type === 'percent' ? '%' : 'VNĐ'"></span>) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="discount_value" min="1" :max="editForm.discount_type === 'percent' ? 100 : null" x-model="editForm.discount_value" required class="input-base w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Đơn tối thiểu (VNĐ)</label>
                                <input type="number" name="min_amount" min="0" x-model="editForm.min_amount" class="input-base w-full">
                            </div>
                            <div x-show="editForm.discount_type === 'percent'">
                                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Giảm tối đa (VNĐ)</label>
                                <input type="number" name="max_discount" min="0" x-model="editForm.max_discount" class="input-base w-full">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1" x-model="editForm.is_active" class="rounded border-zinc-300 text-primary-600 focus:ring-primary-500">
                            <label for="edit_is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Đang hoạt động (cho phép khách đổi điểm)</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" @click="editModalOpen = false" class="btn-secondary">Hủy</button>
                            <button type="submit" class="btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-admin-layout>
