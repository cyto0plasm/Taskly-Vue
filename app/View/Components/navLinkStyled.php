<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class navLinkStyled extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct( public string $title,
        public string $route,
        public float $textSize = 21.8,public string $textColor='')
    {
       
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav-link-styled');
    }
}