<x-admin-layout :title="'Người dùng'">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Người dùng</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quản lý tài khoản khách hàng và chủ sân</p>
    </div>

    {{-- Filter bar --}}
    <div class="card-base p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="label-eyebrow block mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tên hoặc email..."
                       class="input-base">
            </div>
            <div>
                <label class="label-eyebrow block mb-1">Role</label>
                <select name="role" class="input-base w-auto">
                    <option value="">Tất cả</option>
                    <option value="customer" @selected(request('role') === 'customer')>Khách hàng</option>
                    <option value="owner" @selected(request('role') === 'owner')>Chủ sân</option>
                </select>
            </div>
            <div>
                <label class="label-eyebrow block mb-1">Trạng thái</label>
                <select name="status" class="input-base w-auto">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                    <option value="banned" @selected(request('status') === 'banned')>Bị khóa</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Lọc</button>
                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng --}}
    <div class="card-base">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
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
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="p-4 text-zinc-500 dark:text-zinc-400">{{ $users->firstItem() + $index }}</td>
                        <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-50">{{ $user->name }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300">{{ $user->email }}</td>
                        <td class="p-4 text-zinc-600 dark:text-zinc-300">{{ $user->phone ?? '—' }}</td>
                        <td class="p-4">
                            @if($user->isOwner())
                                <x-badge variant="info">Chủ sân</x-badge>
                            @else
                                <x-badge variant="default">Khách hàng</x-badge>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->isActive())
                                <x-badge variant="success">Hoạt động</x-badge>
                            @else
                                <x-badge variant="danger">Bị khóa</x-badge>
                            @endif
                        </td>
                        <td class="p-4 text-zinc-500 dark:text-zinc-400 text-sm">{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td class="p-4 text-center">
                            @if($user->isActive())
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            @click="if(!confirm(`Chắc chắn khóa tài khoản ${@js($user->name)}?`)) $event.preventDefault()"
                                            class="px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl text-xs font-semibold transition"
                                            x-data="">Khóa</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl text-xs font-semibold transition">Mở khóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-4">
                            <x-empty-state icon="heroicons-o-users" title="Không có người dùng nào"
                                           description="Chưa có tài khoản nào khớp với bộ lọc hiện tại." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
