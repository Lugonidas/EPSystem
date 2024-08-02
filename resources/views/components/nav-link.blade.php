@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-1 p-2 text-white font-bold transition-all bg-indigo-800 '
            : 'flex items-center gap-1 p-2 text-white transition-all hover:bg-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
