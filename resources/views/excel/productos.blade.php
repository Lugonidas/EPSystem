<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excel Productos</title>
</head>

<body>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Código</th>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Precio</th>
                    <th class="px-4 py-2">Stock</th>
                    <th class="px-4 py-2">Categoria</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr class="text-center" wire:key='{{ $producto->id }}'>
                        <td class="border px-4 py-2 capitalize">{{ $producto->codigo }}</td>
                        <td class="border px-4 py-2 capitalize">
                            {{ $categoria['estado'] === 1 ? 'Activo' : 'Inactivo' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</body>

</html>
