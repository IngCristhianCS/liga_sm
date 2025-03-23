@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-muted']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
