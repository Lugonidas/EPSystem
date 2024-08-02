<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excel Categorías</title>
</head>

<body>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categorias as $categoria)
                    <tr class="text-center" wire:key='{{ $categoria->id }}'>
                        <td class="border px-4 capitalize">{{ $categoria->nombre}}</td>
                        <td class="border px-4 capitalize">
                            {{ $categoria->estado === 1 ? 'Activo' : 'Inactivo' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</body>

</html>
