{{-- components/nav-link.blade.php --}}
{{-- @props(['textSize'=>16]) --}}
<a href="{{ route($route) }}"
    {{ $attributes->merge([
        'class' =>
            "
               inline-block px-1 font-medium transition-colors duration-300 relative
               after:content-[''] after:absolute after:bottom-[-2px] after:left-0 after:w-0
               after:h-[3px] after:bg-gradient-to-r after:from-violet-600 after:to-indigo-600
               after:transition-all after:duration-300 after:rounded-full
               hover:after:w-full focus:after:w-full
               " . (request()->routeIs($route) ? 'after:w-full' : 'after:w-0 hover:after:w-full focus:after:w-full'),
    ]) }}>
    <span class="{{ $textColor }}">{{ $title }}</span>
</a>
