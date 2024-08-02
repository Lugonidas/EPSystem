@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'bg-red-100 px-1 py-2 text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
