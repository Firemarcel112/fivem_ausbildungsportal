@use('App\DTO\SimpleListItem')
@use('Illuminate\Support\Collection')
@php
    /** @var Collection<int, SimpleListItem> $getGenders */
@endphp

@props([
    'label' => $attributes->get('label', __('general.geschlecht')),
    'name' => $attributes->get('name', 'gender'),
    'required' => $attributes->has('required'),
])

<x-forms.select :$required :label="$label" :name="$name">
    <option disabled selected value="">-- {{ __('general.bitte_waehlen') }} --</option>
    @foreach ($getGenders ?? [] as $gender)
        <option @selected($gender->id == ($selected ?? '')) value="{{ $gender->id }}">{{ $gender->label }}</option>
    @endforeach
</x-forms.select>
