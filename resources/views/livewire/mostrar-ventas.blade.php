<div class="md:p-6 relative">

    <h2 class="text-4xl text-center font-black mb-2 text-gray-600 ">Ventas</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:gap-4 my-2">
            <a target="_blank" href="{{ route('generarPDFVentas') }}"
                class="mb-2 flex gap-1 px-5 py-2.5 text-sm font-medium text-white items-center justify-center bg-indigo-700 hover:bg-indigo-800 focus:outline-none focus:ring-0 rounded-sm text-center">
                <i class="fa-solid fa-download"></i>
                Descargar Reporte
            </a>
        </div>

        <div class="relative mb-4 grid grid-cols-2 gap-4 items-center">
            <div class="flex items-center bg-indigo-100 px-2 rounded-sm">
                <label for="busqueda">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </label>
                <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda"
                    placeholder="No. Factura: Ej: 0000006" class="border-none focus:ring-0 p-2 w-full bg-transparent"
                    autofocus>
            </div>

            <div class="flex justify-end gap-4">
                <div>
                    <label for="fechaInicio" class="mr-2 font-bold">Fecha Inicio:</label>
                    <input wire:model.live="fechaInicio" type="date" id="fechaInicio" name="fechaInicio"
                        class="border-none bg-indigo-100 focus:ring-0 p-2">
                </div>

                <div>
                    <label for="fechaFin" class="mr-2 font-bold">Fecha Fin:</label>
                    <input wire:model.live="fechaFin" type="date" id="fechaFin" name="fechaFin"
                        class="border-none focus:ring-0 p-2 bg-indigo-100">
                </div>
            </div>

        </div>

        @if (count($ventas) === 0)
            <p class="text-2xl text-center font-bold">Aún no has creado productos</p>
        @else
            <!-- Lista de productos -->
            <div class="overflow-x-auto overflow-hidden">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">No. Factura</th>
                            <th class="px-4 py-2">Total</th>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $venta)
                            <tr class="text-center hover:bg-indigo-100" wire:key='{{ $venta->id }}'>
                                <td class="border px-4 capitalize">{{ $venta->numero_factura }}</td>
                                <td class="border px-4">$ {{ $this->formatearDinero($venta->total) }}</td>
                                <td class="border px-4 capitalize">{{ $venta->created_at }}</td>
                                <td class="border px-4 flex items-center justify-center gap-2 w-full">
                                    <a target="_blank" href="{{ route('generarPDFRecibos', ['ventas' => $venta->id]) }}"
                                        class="text-white px-4 py-1 bg-indigo-600 my-2 transition-all hover:scale-105 capitalize">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>

</div>
