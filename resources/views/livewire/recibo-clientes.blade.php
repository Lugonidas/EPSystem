<div>
    <!-- Lista de usuarios -->
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
                    <tr class="text-center hover:bg-gray-400" wire:key='{{ $cliente->id }}'>
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
</div>
