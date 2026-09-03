{{-- ================================================================
     PANEL SIDEBAR — khung sidebar dùng chung cho Owner & Admin
     Props:
        homeUrl  : link của logo (dashboard của vai trò)
        alt      : alt text của logo
        badge    : nhãn nhỏ dưới wordmark (VD: "Quản lý sân", "Admin Panel")
     $sections: mảng [tên nhóm => [[route, icon, label], ...]]
     LƯU Ý: cần bọc trong element có x-data="{ sidebarOpen: false }"
================================================================= --}}
@props(['homeUrl' => '/', 'alt' => 'Arena Sports Booking', 'badge' => '', 'sections' => []])

{{-- Overlay mobile/tablet --}}
<div x-show="sidebarOpen"
    @click="sidebarOpen = false"
    x-transition.opacity
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

{{-- Sidebar --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-72 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800
              transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto
              flex flex-col">

    {{-- Logo --}}
    <div class="flex items-center h-16 px-5 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <a href="{{ $homeUrl }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('images/logo/logo.jpg') }}" alt="{{ $alt }}"
                class="w-9 h-9 rounded-lg object-cover shrink-0 ring-1 ring-zinc-200 dark:ring-zinc-700">
            <div class="leading-tight min-w-0">
                <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Arena</span>
                <span class="block text-[10px] font-medium text-primary-600 dark:text-primary-400 uppercase tracking-widest truncate">{{ $badge }}</span>
            </div>
        </a>
    </div>

    {{-- Nav sections — mỗi section: [[route, icon, label], ...] --}}
    <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
        @foreach($sections as $title => $links)
        <div>
            @if($title)
            <p class="px-3 mb-2 text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest select-none">
                {{ $title }}
            </p>
            @endif
            <div class="space-y-1">
                @foreach($links as [$route, $icon, $label])
                <x-sidebar-link href="{{ route($route) }}" :active="request()->routeIs($route)" icon="{{ $icon }}">
                    {{ $label }}
                </x-sidebar-link>
                @endforeach
            </div>
        </div>
        @endforeach
    </nav>

    {{-- Bottom --}}
    <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 text-center">
            Arena Sports Booking &copy; {{ date('Y') }}
        </p>
    </div>
</aside>