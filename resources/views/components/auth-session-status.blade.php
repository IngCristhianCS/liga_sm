@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'fw-medium fs-1 text-success']) }}>
        {{ $status }}
    </div>
@endif
