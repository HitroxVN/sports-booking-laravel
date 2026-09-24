{{-- Checkbox tiện ích — dùng chung create + edit. Truyền vào: $selected (mảng value đã chọn) --}}
@php $selected = $selected ?? []; @endphp
<div class="mb-6">
    <label class="label-eyebrow block mb-3">Tiện ích tại sân</label>
    <div class="flex flex-wrap gap-6 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-800">
        @foreach (\App\Models\Venue::AMENITY_LABELS as $value => $label)
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="amenities[]" value="{{ $value }}" @checked(in_array($value, $selected))
                       class="rounded border-zinc-300 dark:border-zinc-600 text-primary-600 focus:ring-primary-500 w-5 h-5 bg-white dark:bg-zinc-800">
                <span class="ml-2 font-medium text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    @error('amenities') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
