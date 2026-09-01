@props(['variant' => 'default'])

@php
$variantClasses = [
    'success' => 'badge bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    'warning' => 'badge bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
    'danger' => 'badge bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
    'info' => 'badge bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    'default' => 'badge bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300',
][$variant];
@endphp

<span {{ $attributes->merge(['class' => $variantClasses]) }}>
    {{ $slot }}
</span>
