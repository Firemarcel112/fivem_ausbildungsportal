<div @class(['form-floating', ...$classes])>
    <select {{ $attributes ?? '' }} @if ($required) required @endif @if ($multiple) multiple @endif class="form-select selectpicker w-100" data-live-search="true" data-style="tabler-select" id="{{ $name }}" name="{{ $name }}" title="{{ $title }}">
        {{ $slot }}
    </select>
    <label @class(['form-label', 'required' => $required]) for="{{ $name }}">{{ $label }}</label>
</div>
@error($name)
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
@enderror
