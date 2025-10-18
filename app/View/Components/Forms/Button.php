<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
	public string $color;
	public array $classes;
	/**
	 * Create a new component instance.
	 */
	public function __construct($color = 'primary', $classes = [])
	{
		$this->color = $color;
		$this->classes = $classes;
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.forms.button');
	}
}
