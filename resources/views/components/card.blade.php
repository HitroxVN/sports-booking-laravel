<div {{ $attributes->merge(['class' => 'card-base' . ($hover ? ' card-hover' : '') . ($padding ? ' p-5' : '')]) }}>
    {{ $slot }}
</div>
