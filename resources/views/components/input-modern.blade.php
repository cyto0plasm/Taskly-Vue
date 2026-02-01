@props([
    'type' => 'text',
    'id' => '',
    'name' => '',
    'placeholder' => '',
    'label' => '',
    'field' => 'input',
    'value' => '',
])

@php
    // Determine initial type
    $inputType = $name == 'password' || $name == 'confirm-password' ? 'password' : $type;
@endphp

<div class="relative text-gray-700 dark:text-white focus-within:text-indigo-500 transition-colors duration-200">
    <label for="{{ $id ?: $name }}" class="block text-sm font-medium mb-2 ml-2 select-none">
        {{ $label }}
    </label>

    <div class="relative">

        {{-- INPUT --}}
        @if ($field === 'input')
            <input type="{{ $inputType }}" name="{{ $name }}" id="{{ $id }}"
                placeholder="{{ $placeholder }}"
                autocomplete="{{ $name === 'password' || $name === 'confirm-password' ? 'new-password' : 'off' }}"
                value="{{ old($name, $value) }}"
                class="w-full h-12 pl-12 pr-12
                       bg-white border-2 border-gray-200 rounded-xl
                       text-gray-900 placeholder:text-gray-400
                       outline-none transition-all duration-200 ease-out
                       hover:border-gray-300 focus:border-indigo-500
                       shadow-sm hover:shadow focus:shadow-md" />
        @endif

        {{-- TEXTAREA --}}
        @if ($field === 'textarea')
            <textarea name="{{ $name }}" id="{{ $id }}" placeholder="{{ $placeholder }}"
                class="w-full min-h-28 pl-12 pr-4 py-3
                       bg-white border-2 border-gray-200 rounded-xl
                       text-gray-900 placeholder:text-gray-400
                       resize-y   {{-- <-- ENABLE VERTICAL RESIZE --}}
                       outline-none transition-all duration-200 ease-out
                       hover:border-gray-300 focus:border-indigo-500
                       shadow-sm hover:shadow focus:shadow-md">{{ old($name, $value) }}</textarea>
        @endif

        {{-- ICONS --}}
        <div
            class="absolute left-1
        {{ $field === 'textarea' ? 'top-3 translate-y-2' : 'top-1/2 -translate-y-1/2' }}
        text-gray-400 pointer-events-none">

            @if ($name == 'name')
                <x-svg.user-icon />
            @elseif ($name == 'email')
                <x-svg.email-icon />
            @elseif ($name == 'bio')
                <x-svg.info />
            @elseif ($name == 'password' || $name == 'password_confirmation')
                <x-svg.lock-icon />
            @endif
        </div>

        {{-- PASSWORD TOGGLE --}}
        @if ($field === 'input')
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                onclick="togglePassword(this)">
                <svg class="eye-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <svg class="eye-hide hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.94 17.94A10.06 10.06 0 0 1 12 20c-7 0-11-8-11-8a21.28 21.28 0 0 1 5.06-6.06M3 3l18 18" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                </svg>
            </button>
        @endif
    </div>
</div>

<script>
    function togglePassword(button) {
        const container = button.parentElement;
        const input = container.querySelector('input');
        const eyeShow = button.querySelector('.eye-show');
        const eyeHide = button.querySelector('.eye-hide');

        if (!input) return;

        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        eyeShow.classList.toggle('hidden', show); // hide eye-show when showing text
        eyeHide.classList.toggle('hidden', !show); // show eye-hide when showing text
    }
</script>
