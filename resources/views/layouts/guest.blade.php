<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EPSystem') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
</head>

<body class="font-sans text-gray-900 antialiased backdrop-blur-sm"
    style="background-image: url('https://images.pexels.com/photos/305821/pexels-photo-305821.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-repeat: no-repeat; background-size: cover;">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 filter">
        <div>
            <a href="/" wire:navigate>
                <x-application-logo />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg bg-gray-600">
            {{ $slot }}
        </div>

    </div>
</body>

</html>
