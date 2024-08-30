<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excel Usuarios</title>
</head>

<body>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Número Identificación</th>
                    <th class="px-4 py-2">Usuario</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr class="text-center" wire:key='{{ $usuario->id }}'>
                        <td class="border px-4 capitalize">{{ $usuario->name }}</td>
                        <td class="border px-4 capitalize">{{ $usuario->numero_identificacion }}</td>
                        <td class="border px-4 capitalize">{{ $usuario->usuario }}</td>
                        <td class="border px-4 capitalize">{{ $usuario->email }}</td>
                        <td class="border px-4 capitalize">{{ $usuario->estado == 1 ? 'Activo' : 'Inactivo' }}</td>
                        <td class="border px-4 capitalize">{{ $usuario->rol == 1 ? 'Admin' : 'Cajero' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
