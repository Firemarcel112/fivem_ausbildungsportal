<div @class(['card', 'card-' . $size ?? 'md', ...$classes ?? []])>
    @if (!empty($header))
        <div class="card-header">
            {{ $header }}
        </div>
    @endif
    <div class="card-body {{ $attributes['card-body-classes'] ?? '' }}">
        {{ $body ?? '' }}
    </div>
    @if (!empty($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
