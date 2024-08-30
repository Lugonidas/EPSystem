<div>
    <!-- Lista de usuarios -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">No. Identificación</th>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Usuario</th>
                    <th class="px-4 py-2">Correo</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr class="text-center" wire:key='{{ $usuario->id }}'>
                        <td class="border px-4 py-2 capitalize">{{ $usuario->numero_identificacion }}</td>
                        <td class="border px-4 py-2 capitalize">{{ $usuario->name }}</td>
                        <td class="border px-4 py-2 capitalize">{{ $usuario->usuario }}</td>
                        <td class="border px-4 py-2 capitalize">{{ $usuario->email }}</td>
                        <td class="border px-4 py-2 capitalize">
                            {{ $usuario->estado == 1 ? 'Activo' : 'No Activo' }}</td>
                        <td class="border px-4 py-2 capitalize">{{ $usuario->rol == 1 ? 'Admin' : 'Cajero' }}</td>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
