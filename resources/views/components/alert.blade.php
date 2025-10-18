<div @class([
    'alert',
    $getClass(),
    'alert-dismissible' => empty($attributes['static']),
]) role="alert">
    <div class="alert-icon">
        <x-icon :classes="['icon', 'alert-icon', 'icon-2']" :name="$getIcon" />
    </div>
    {!! $slot !!}
    @if (empty($attributes['static']))
        <a aria-label="close" class="btn-close" data-bs-dismiss="alert" role="alertdialog"></a>
    @endif
</div>
