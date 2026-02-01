<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Mockery\Matcher\Any;

class input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct( public string $name , public string $placeholder , public mixed  $value =null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}