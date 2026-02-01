@props(['colorHex' => '#10B981', 'label', 'name', 'options' => [], 'selected' => null])
<div>
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
    </label>
    <select id="{{ $name }}" name="{{ $name }}"
        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[{{ $colorHex }}] focus:border-transparent transition-all">
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>{{ $label }}
            </option>
        @endforeach
    </select>
</div>
