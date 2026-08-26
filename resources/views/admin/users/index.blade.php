<x-admin-layout :title="'Người dùng'">

    {{-- Filter bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tên hoặc email..."
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Role</label>
                <select name="role" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="customer" @selected(request('role') === 'customer')>Khách hàng</option>
                    <option value="owner" @selected(request('role') === 'owner')>Chủ sân</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Trạng thái</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                    <option value="banned" @selected(request('status') === 'banned')>Bị khóa</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">Lọc</button>
                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">STT</th>
                        <th class="p-4 font-semibold">Tên</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">SĐT</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Trạng thái</th>
                        <th class="p-4 font-semibold">Ngày tạo</th>
                        <th class="p-4 font-semibold text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-500">{{ $users->firstItem() + $index }}</td>
                        <td class="p-4 font-semibold text-gray-800">{{ $user->name }}</td>
                        <td class="p-4 text-gray-600">{{ $user->email }}</td>
                        <td class="p-4 text-gray-600">{{ $user->phone ?? '—' }}</td>
                        <td class="p-4">
                            @if($user->isOwner())
                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">Chủ sân</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Khách hàng</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->isActive())
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Hoạt động</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Bị khóa</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500 text-sm">{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td class="p-4 text-center">
                            @if($user->isActive())
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            @click="if(!confirm(`Chắc chắn khóa tài khoản ${@js($user->name)}?`)) $event.preventDefault()"
                                            class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition"
                                            x-data="">Khóa</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-bold transition">Mở khóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-gray-400">Không có người dùng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
