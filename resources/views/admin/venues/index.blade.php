<x-admin-layout :title="'Khu sân'">

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
               class="px-4 py-2 rounded-lg text-sm font-bold transition
                      {{ request('status', '') === $tab['value']
                          ? 'bg-gray-900 text-white'
                          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                {{ $tab['label'] }}
                @if($tab['value'] === 'pending')
                    <span class="ml-1 {{ request('status') === 'pending' ? 'bg-red-100 text-red-600' : 'bg-red-100 text-red-600' }} px-2 py-0.5 rounded-full text-xs">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Modal từ chối dùng chung --}}
    <div x-data="{ open: false, venueSlug: null, venueName: '' }">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Tên</th>
                            <th class="p-4 font-semibold">Chủ sân</th>
                            <th class="p-4 font-semibold">Địa chỉ</th>
                            <th class="p-4 font-semibold">Trạng thái</th>
                            <th class="p-4 font-semibold">Lý do từ chối</th>
                            <th class="p-4 font-semibold text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($venues as $venue)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-semibold text-gray-800">{{ $venue->name }}</td>
                            <td class="p-4 text-gray-600">{{ $venue->owner->name ?? '—' }}</td>
                            <td class="p-4 text-gray-600 text-sm">{{ $venue->district }}, {{ $venue->city }}</td>
                            <td class="p-4">
                                @if($venue->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Chờ duyệt</span>
                                @elseif($venue->status === 'active')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Đã duyệt</span>
                                @elseif($venue->status === 'rejected')
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Đã từ chối</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">Đóng cửa</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-500 text-sm max-w-xs whitespace-normal">
                                @if($venue->reject_reason)
                                    <span title="Nhấp để xem đầy đủ" class="block truncate cursor-pointer hover:text-gray-700"
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
                                            <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-bold transition">Duyệt</button>
                                        </form>
                                        <button type="button"
                                                @click="open = true; venueSlug = @js($venue->slug); venueName = @js($venue->name)"
                                                class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition">Từ chối</button>
                                    </div>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-400">Không có khu sân nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($venues->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $venues->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- Modal từ chối dùng chung --}}
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             @keydown.escape.window="open = false">
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6" @click.outside="open = false">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Từ chối khu sân</h3>
                <p class="text-sm text-gray-500 mb-4" x-text="`Từ chối khu sân: ${venueName}`"></p>
                <form method="POST" :action="`/admin/venues/${venueSlug}/reject`">

                    @csrf
                    <label class="block text-xs font-bold text-gray-500 mb-1">Lý do từ chối</label>
                    <textarea name="reason" required maxlength="500" rows="4"
                              placeholder="Nhập lý do từ chối..."
                              class="w-full rounded-lg border-gray-300 text-sm focus:ring-red-500 focus:border-red-500"></textarea>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-200">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700">Xác nhận từ chối</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
