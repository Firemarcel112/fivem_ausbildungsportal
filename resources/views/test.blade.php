@extends('layouts.app')

@section('content')
    <x-forms.select.qualifications :label="__('general.qualifikation')" name="qualification_id" />
@endsection
