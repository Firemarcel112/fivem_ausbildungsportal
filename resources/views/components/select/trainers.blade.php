@use('App\DTO\SimpleListItem')
@use('Illuminate\Support\Collection')
@php
    /** @var Collection<int, SimpleListItem> $getTrainers */
@endphp

@props([
    'label' => $attributes->get('label', __('general.ausbilder')),
    'name' => $attributes->get('name', 'trainer_id'),
    'required' => $attributes->has('required'),
    'selected_id' => $attributes->get('selected-id', 0),
])

<x-forms.select :$attributes :$required :label="$label" :name="$name">
    <option disabled selected value="">-- {{ __('general.bitte_waehlen') }} --</option>
    @foreach ($getTrainers ?? [] as $trainer)
        <option @selected($selected_id == $trainer->id) value="{{ $trainer->id }}">
            {{ $trainer->label }}
        </option>
    @endforeach
</x-forms.select>
