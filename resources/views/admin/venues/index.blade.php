<x-admin-layout :title="'Khu sân'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Khu sân</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quản lý và phê duyệt các khu sân trong hệ thống</p>
    </div>

    {{-- Tabs theo status --}}
    @php
        $tabs = [
            ['label' => 'Tất cả', 'value' => ''],
            ['label' => 'Chờ duyệt', 'value' => 'pending'],
            ['label' => 'Đã duyệt', 'value' => 'active'],
            ['label' => 'Đã từ chối', 'value' => 'rejected'],
        ];
    @endphp

    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($tabs as $tab)
            <a href="{{ route('admin.venues.index', $tab['value'] ? ['status' => $tab['value']] : []) }}"
               class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition
                      {{ request('status', '') === $tab['value']
                          ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900'
                          : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                {{ $tab['label'] }}
                @if($tab['value'] === 'pending')
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ $pendingCount }}</span>
                @endif
                @if($tab['value'] === '' && $pendingCount > 0)
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Modal từ chối dùng chung --}}
    <div x-data="{ open: false, venueSlug: null, venueName: '' }">
        <div class="card-base">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Tên</th>
                            <th class="p-4 font-semibold">Chủ sân</th>
                            <th class="p-4 font-semibold">Địa chỉ</th>
                            <th class="p-4 font-semibold">Trạng thái</th>
                            <th class="p-4 font-semibold">Lý do từ chối</th>
                            <th class="p-4 font-semibold text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($venues as $venue)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-50">{{ $venue->name }}</td>
                            <td class="p-4 text-zinc-600 dark:text-zinc-300">{{ $venue->owner->name ?? '—' }}</td>
                            <td class="p-4 text-zinc-600 dark:text-zinc-300 text-sm">{{ $venue->district }}, {{ $venue->city }}</td>
                            <td class="p-4">
                                @if($venue->status === 'pending')
                                    <x-badge variant="warning">Chờ duyệt</x-badge>
                                @elseif($venue->status === 'active')
                                    <x-badge variant="success">Đã duyệt</x-badge>
                                @elseif($venue->status === 'rejected')
                                    <x-badge variant="danger">Đã từ chối</x-badge>
                                @else
                                    <x-badge variant="default">Đóng cửa</x-badge>
                                @endif
                            </td>
                            <td class="p-4 text-zinc-500 dark:text-zinc-400 text-sm max-w-xs whitespace-normal">
                                @if($venue->reject_reason)
                                    <span title="Nhấp để xem đầy đủ" class="block truncate cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300"
                                          @click="alert(@js($venue->reject_reason))">{{ $venue->reject_reason }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($venue->status === 'pending')
                                    <div class="flex justify-center gap-2">
                                        <form method="POST" action="{{ route('admin.venues.approve', $venue) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl text-xs font-semibold transition">Duyệt</button>
                                        </form>
                                        <button type="button"
                                                @click="open = true; venueSlug = @js($venue->slug); venueName = @js($venue->name)"
                                                class="px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl text-xs font-semibold transition">Từ chối</button>
                                    </div>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4">
                                <x-empty-state icon="heroicons-o-inbox" title="Không có khu sân nào"
                                               description="Không có khu sân nào khớp với tab hiện tại." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($venues->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $venues->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- Modal từ chối dùng chung --}}
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             @keydown.escape.window="open = false">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg w-full max-w-md p-6 border border-zinc-200 dark:border-zinc-800" @click.outside="open = false">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-2">Từ chối khu sân</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4" x-text="`Từ chối khu sân: ${venueName}`"></p>
                <form method="POST" :action="`/admin/venues/${venueSlug}/reject`">

                    @csrf
                    <label class="label-eyebrow block mb-1">Lý do từ chối</label>
                    <textarea name="reason" required maxlength="500" rows="4"
                              placeholder="Nhập lý do từ chối..."
                              class="input-base focus:border-red-500 focus:ring-red-500/20"></textarea>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="open = false" class="btn-secondary">Hủy</button>
                        <button type="submit" class="btn-danger">Xác nhận từ chối</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
