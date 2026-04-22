<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * Create a new component instance.
     *
     * @param array<string> $classes
     */
    public function __construct(
        public string $name,
        public array $classes = [],
        public int $widthHeight = 24,
        public string $hovertext = '',
        public bool $enableHtml = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icon', [
            'width_height' => $this->widthHeight,
            'name' => $this->name,
            'classes' => implode(' ', $this->classes),
        ]);
    }

    public function getHoverTextAttributes(): string
    {
        $attributes = [];

        if (!empty($this->hovertext)) {
            $attributes[] = 'data-bs-toggle="tooltip"';
            $attributes[] = 'data-bs-html="true"';
            $attributes[] = 'data-bs-title="' . ($this->enableHtml ? $this->hovertext : htmlspecialchars($this->hovertext)) . '"';
        }

        return implode(' ', $attributes);
    }
}
