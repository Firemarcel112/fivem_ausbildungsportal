<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public mixed $value;

    public string $name;

    public array $classes;

    public string $label;

    public bool $required;

    public bool $multiple;

    public ?string $title;

    public array $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        mixed $value = null,
        array $classes = [],
        mixed $class = null,
        bool $required = false,
        bool $multiple = false,
        ?string $title = null,
    ) {
        $this->name = $name;
        $this->value = $value ?? old($name, null);
        $this->classes = $classes;
        $this->label = $label;
        $this->required = $required;
        if (empty($class)) {
            $class = [];
        }

        if (is_string($class)) {
            $this->class = explode(' ', $class);
        }

        $this->class = $class;

        if ($multiple) {
            $this->name .= '[]';
        }
        $this->multiple = $multiple;
        $this->title = $title ?? __('general.bitte_waehlen');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
