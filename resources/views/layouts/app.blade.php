<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="EPSystem es un avanzado Sistema POS diseñado para optimizar y gestionar eficientemente tus ventas y operaciones comerciales. Con características innovadoras, soporte técnico confiable y una interfaz intuitiva, EPSystem te ofrece una solución completa para llevar tu negocio al siguiente nivel. Descubre cómo nuestra tecnología puede transformar tu punto de venta hoy mismo.">

    <title>{{ config('app.name', 'EPSystem') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class=" relative">

        <livewire:layout.navigation />

        <!-- Page Heading -->
        {{--         @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif --}}

        <!-- Page Content -->
        <main class="overflow-hidden min-h-screen px-4 pt-10 pb-4">
            {{ $slot }}
        </main>

    </div>

    @stack('scripts')
</body>

</html>
