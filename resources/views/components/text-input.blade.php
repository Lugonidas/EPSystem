@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0']) !!}>
