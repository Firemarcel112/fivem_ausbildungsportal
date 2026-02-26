<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public string $size;

    public array $classes;

    /**
     * Create a new component instance.
     */
    public function __construct($size = 'md', array $classes = [])
    {
        $this->size = $size;
        $this->classes = $classes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}
