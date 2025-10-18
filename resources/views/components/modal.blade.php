<div aria-hidden="true" aria-labelledby="{{ $title }}" class="modal fade" id="{{ $attributes['id'] }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $title }}">{{ $title }}</h5>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            @if (!empty($attributes['form_action']))
                @php
                    $form_method = 'POST';
                    if (!empty($attributes['form_method'])) {
                        $form_method = $attributes['form_method'];
                    }
                @endphp
                <form action="{{ $attributes['form_action'] }}" enctype="multipart/form-data" method="{{ $form_method }}">
                    @if ($form_method == 'POST')
                        @csrf
                    @endif
            @endif
            @if (!empty($body))
                <div class="modal-body {{ $attributes['body_classes'] ?? '' }}">
                    @if (!empty($attributes['form_action']))
                        <div class="space-y">
                            {{ $body }}
                        </div>
                    @else
                        {{ $body }}
                    @endif
                </div>
            @endif
            @if (!empty($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
            @if (!empty($attributes['form_action']))
                </form>
            @endif
        </div>
    </div>
</div>
