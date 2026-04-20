@if ($type != 'password')
    <div @class([
        'form-floating',
        'd-flex' => $type == 'password',
        ...$classes,
    ])>
        <input {{ $attributes ?? '' }} @if ($disabled) disabled @endif @if ($required) required @endif class="form-control" id="{{ $name }}" name="{{ $name }}" placeholder="{{ $slot }}" type="{{ $type }}" value="{{ $getValue() }}"></input>
        <label @class(['form-label', 'required' => $required]) for="{{ $name }}">{{ $slot }}</label>
    </div>
@endif

@if ($type == 'password')
    <div @class(['input-group', 'input-group-flat', ...$classes])>
        <div class="form-floating">
            <input @if ($required) required @endif class="form-control" id="{{ $name }}" name="{{ $name }}" placeholder="{{ $slot }}" type="{{ $type }}" value="{{ $getValue() }}">
            <label @class(['form-label', 'required' => $required]) for="{{ $name }}">{{ $slot }}</label>
        </div>
        <span class="input-group-text app-js-toggle-password">
            <x-icon :hovertext="__('general.passwort_anzeigen')" name="eye" />
        </span>
    </div>
@endif
@error($name)
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
@enderror
