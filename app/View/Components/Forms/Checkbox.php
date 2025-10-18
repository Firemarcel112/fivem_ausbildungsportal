<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
	public int $value;
	public string $name;
	public array $classes;
	public array $input_classes;
	public bool $with_hidden_value;

	/**
	 * Create a new component instance.
	 */
	public function __construct(string $name, int $value = 0, $with_hidden_value = false, array $classes = [], array $input_classes = [])
	{
		$this->name = $name;
		$this->value = $value ?? old($name, default: 0);
		$this->classes = $classes;
		$this->input_classes = $input_classes;
		$this->with_hidden_value = $with_hidden_value;
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.forms.checkbox');
	}
}
