<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    private ?User $user;

    public string $size;

    public ?string $image;

    public ?string $initials;

    /**
     * Create a new component instance.
     */
    public function __construct(?User $user, string $size = 'md', ?string $initials = null)
    {
        $this->user = $user;
        $this->size = $size;
        $this->image = $this->user?->discord?->avatar ?? null;
        $this->initials = $initials ?? $this->user->getInitials();
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
