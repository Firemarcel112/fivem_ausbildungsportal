<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Paginate extends Component
{
	public mixed $data;
	/**
	 * Create a new component instance.
	 */
	public function __construct(mixed $data = [])
	{
		$this->data = $data;
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.paginate', [
			'data' => $this->data,
		]);
	}
}
