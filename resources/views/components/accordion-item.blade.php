@props([
    'target_id' => 'accordion-' . uniqid(),
    'header' => 'Accordion Header',
    'body' => 'Accordion Body',
])
<div class="accordion-item">
    <div class="accordion-header">
        <button aria-expanded="false" class="accordion-button collapsed" data-bs-target="#{{ $target_id }}" data-bs-toggle="collapse" type="button">
            {{ $header ?? '' }}
            <div class="accordion-button-toggle">
                <x-icon name="chevron-down" />
            </div>
        </button>
    </div>
    <div class="accordion-collapse collapse" data-bs-parent="#accordion-flush" id="{{ $target_id }}" style="">
        <div class="accordion-body">
            {{ $body ?? '' }}
        </div>
    </div>
</div>
