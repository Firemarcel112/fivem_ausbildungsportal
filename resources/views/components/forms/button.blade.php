<button {{ $attributes ?? '' }} @class(['btn', 'btn-' . $color, ...$classes]) type="submit">
    <span class="me-1">{{ $slot }}</span>
    <span class="spinner-border spinner-border-sm d-none"></span>
</button>
