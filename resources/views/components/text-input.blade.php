@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'p-1 ring-0 transition-border focus:border-gray-300 focus:ring-0']) !!}>
