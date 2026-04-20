<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public mixed $value;

    public string $name;

    public ?string $id;

    public array $classes;

    public string $type;

    public ?string $default;

    public bool $disabled;

    public bool $required;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        mixed $value = null,
        ?string $default = null,
        array $classes = [],
        string $type = 'text',
        ?string $id = null,
        bool $required = false,
        bool $disabled = false,
    ) {
        $this->name = $name;
        $this->default = $default;
        $this->value = $value ?? old($name, $this->default);
        $this->classes = $classes;
        $this->type = $type;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->id = $id;

        if (empty($this->id)) {
            $this->id = $name . '-input';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input');
    }

    public function getValue()
    {
        if (session()->hasOldInput($this->name)) {
            return old($this->name);
        }

        if ($this->value !== null) {
            return $this->value;
        }

        if (request()->has($this->name)) {
            return request()->get($this->name);
        }

        return $this->default;
    }
}
