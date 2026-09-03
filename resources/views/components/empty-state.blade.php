@props([
    'icon' => 'heroicons-o-inbox',
    'title' => '',
    'description' => '',
    'actionUrl' => null,
    'actionText' => null,
])

<div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="mb-4">
        @if($icon === 'heroicons-o-calendar')
            <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        @elseif($icon === 'heroicons-o-users')
            <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        @else
            <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>

    @if($title)
        <h3 class="mb-2 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h3>
    @endif

    @if($description)
        <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">{{ $description }}</p>
    @endif

    @if($actionUrl && $actionText)
        <x-primary-button :href="$actionUrl">
            {{ $actionText }}
        </x-primary-button>
    @endif
</div>
