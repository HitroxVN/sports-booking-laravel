@props(['value'])

<label {{ $attributes->merge(['class' => 'label-eyebrow']) }}>
    {{ $value ?? $slot }}
</label>
