@props(['direction' => 'up'])

<svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14">
    @if ($direction === 'up')
        <line x1="6" y1="13" x2="6" y2="4" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
        <polyline points="1,8 6,2 11,8" fill="none" stroke="black" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/>
    @elseif ($direction === 'down')
        <line x1="6" y1="1" x2="6" y2="10" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
        <polyline points="1,6 6,12 11,6" fill="none" stroke="black" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/>
    @endif
</svg>
