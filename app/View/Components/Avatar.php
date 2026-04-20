<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    public string $size;

    public ?string $image;

    public ?string $initials;

    /**
     * Create a new component instance.
     */
    public function __construct(string $size = 'md', string $initials = 'XX')
    {
        $this->size = $size;
        $this->image = null;
        $this->initials = $initials;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.avatar', [
            'avatar_size' => 'avatar-' . $this->size,
        ]);
    }
}
