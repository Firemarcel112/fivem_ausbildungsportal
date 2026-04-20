@php
    /** @var \Illuminate\Support\Collection<int, \App\DTO\SimpleListItem> $getQualifications */
@endphp

@props([
    'label' => $attributes->get('label', __('general.qualifikation')),
    'name' => $attributes->get('name', 'qualification_id'),
    'required' => $attributes->has('required'),
])

<x-forms.select :$required :label="$label" :name="$name">
    <option disabled selected value="">-- {{ __('general.bitte_waehlen') }} --</option>
    @foreach ($getQualifications ?? [] as $qualification)
        <option @selected($isSelected($qualification->id)) value="{{ $qualification->id }}">{{ $qualification->label }}</option>
    @endforeach
</x-forms.select>
