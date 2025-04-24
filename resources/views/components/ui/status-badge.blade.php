@props(['status'])

@php
    $class = match($status) {
        'Active' => 'bg-success text-white',
        'Pending' => 'bg-warning text-dark',
        'Rejected' => 'bg-danger text-white',
        'Disabled' => 'bg-secondary text-white',
        default => 'bg-light text-dark',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge $class"]) }}>
    {{ $status }}
</span>


