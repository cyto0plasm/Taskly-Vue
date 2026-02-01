@props([
    'colorHex' => '#10B981',
    'label',
    'name',
    'rows' => 3,
    'value' => '',
    'placeholder' => '',
])
<div>
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[{{ $colorHex }}] focus:border-transparent transition-all resize-none"
        placeholder="{{ $placeholder }}" autocomplete="off">{{ old($name, $value) }}</textarea>
</div>
