<div>
    <!-- Lista de usuarios -->
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
                    <tr class="text-center hover:bg-gray-400" wire:key='{{ $categoria->id }}'>
                        <td class="border px-4 py-2 capitalize">{{ $categoria->nombre }}</td>
                        <td class="border px-4 py-2 capitalize">
                            {{ $categoria->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
