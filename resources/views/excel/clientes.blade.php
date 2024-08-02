<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excel Clientes</title>
</head>

<body>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Número Identificación</th>
                    <th class="px-4 py-2">Celular</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Ciudad</th>
                    <th class="px-4 py-2">Dirección</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr class="text-center" wire:key='{{ $cliente->id }}'>
                        <td class="border px-4 capitalize">{{ $cliente->nombre }}</td>
                        <td class="border px-4 capitalize">{{ $cliente->numero_identificacion }}</td>
                        <td class="border px-4 capitalize">{{ $cliente->celular }}</td>
                        <td class="border px-4 capitalize">{{ $cliente->email }}</td>
                        <td class="border px-4 capitalize">{{ $cliente->ciudad }}</td>
                        <td class="border px-4 capitalize">{{ $cliente->direccion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
