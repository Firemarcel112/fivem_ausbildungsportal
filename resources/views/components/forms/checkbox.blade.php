<div @if(!empty($classes)) @class([...$classes]) @endif>
	<label id="{{ $name }}" for="{{ $name }}" @class(['form-check', ...$label_classes ?? []])>
		@if($with_hidden_value)
			<input id="{{ $name }}" type="hidden" name="{{ $name }}" value="0">
		@endif
		<input @class(['form-check-input', ...$input_classes])
		name="{{ $name }}"
		type="checkbox"
		@checked($value == 1)
		value="{{ $value ?? 1 }}"
		{{ $attributes ?? '' }}
		>
		<span @class([
			'form-check-label',
			'required' => !empty($attributes['required'])
		])>
			{{ $slot ?? '' }}
		</span>
	</label>
</div>


