@component('mail::message')
# {{ $title }}

@php 
    foreach ($customMessage as $item) {
        echo $item;
    }
@endphp


Thanks,<br>
{{ config('app.name') }}
@endcomponent
