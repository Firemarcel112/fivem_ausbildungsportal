@if (!empty($data) && method_exists($data, 'hasPages') && $data->hasPages())
    <div class="p-3 mx-auto card-footer border-top-0">
        <div class="col-12">
            {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
