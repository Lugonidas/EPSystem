<div class="md:p-6 relative pb-40" wire:keydown.window='teclaPresionada($event.key)'>
    @if ($modalRecibo)
        <div class="transition-all ease-in fixed z-50 bg-[rgba(0,0,0,0.5)] top-0 left-0 w-full p-2 h-screen overflow-y-scroll backdrop-blur-sm">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-gray-950 p-4 rounded-lg w-full max-w-2xl">
                    <h1 class="text-4xl text-center text-indigo-800">Recibo de Venta</h1>
                    <p class="text-xl text-white">Número de Factura: EPS{{ $venta->numero_factura }}</p>
                    <p class="text-xl text-white">Fecha de venta: {{ $venta->created_at }}</p>
                    <p class="text-xl text-white capitalize">Cliente: {{ $cliente['nombre'] ?? 'Super Usuario' }}</p>

                    <!-- Lista de productos -->
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4">Producto</th>
                                <th class="px-4">Cantidad</th>
                                <th class="px-4">Precio</th>
                                <th class="px-4">Total</th>
                            </tr>
                        </thead>
                        <tbody class="w-full overflow-x-scroll">
                            @foreach ($carritoRecibo as $producto)
                                <tr class="text-center" wire:key='$producto->producto_id'>
                                    <td class="border text-white px-4 uppercase">{{ $producto->name }}</td>
                                    <td class="border text-white px-4 uppercase">{{ $producto->quantity }}</td>
                                    <td class="border text-white px-4">${{ $this->formatearDinero($producto->price) }}
                                    </td>
                                    <td class="border text-white px-4">
                                        ${{ $this->formatearDinero($producto->price * $producto->quantity) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p class="text-white">Total de la Venta: <span
                            class="text-2xl font-bold text-indigo-800">${{ $this->formatearDinero($venta->total) }}</span>
                    </p>
                    <p class="text-white">Observaciones: {{ $venta->observaciones }}</p>

                    <div class="flex flex-col md:flex-row md:justify-between md:gap-4">
                        {{--                         <a target="_blank" href="{{ route('generarPDFRecibo', ['venta' => $venta->id]) }}"
                            class="text-white px-4 py-1 bg-indigo-600 my-2 transition-all hover:scale-105 capitalize"
                            onclick="imprimirRecibo();">
                            Imprimir Recibo
                        </a> --}}

                        <button wire:click.prevent="generarPDFRecibo({{ $venta->id }})"
                            class="text-white px-4 py-1 bg-indigo-600 my-2 transition-all hover:scale-105 capitalize">
                            Imprimir Recibo
                        </button>


                        <button wire:click.prevent="cerrarModalRecibo"
                            class="text-white px-4 py-1 bg-gray-600 my-2 transition-all hover:scale-105">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if ($modalClientes)
        <div class="transition-all ease-in fixed z-50 bg-[rgba(0,0,0,0.5)] top-0 left-0 w-full p-2 h-screen overflow-y-scroll backdrop-blur-sm">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-gray-950 p-4 rounded-lg w-full max-w-2xl">

                    <div class="flex items-center bg-white px-2 rounded-sm">
                        <label for="busquedaCliente">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </label>
                        <input wire:model.live="busquedaCliente" type="search" id="busquedaCliente"
                            name="busquedaCliente" placeholder="Cliente: Ej: 1007..."
                            class="border-none focus:ring-0 p-2 w-full focus:border-none">
                    </div>

                    <h2 class="text-4xl text-center mb-2 text-white my-4">Lista Clientes</h2>

                    <div class="overflow-x-auto">
                        @if (count($clientes) == 0)
                            <p class="text-center text-2xl text-white">No se han encontrado clientes</p>
                        @else
                            <!-- Lista de productos -->
                            <table class="min-w-full table-auto overflow-x-scroll">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="px-4 py-2">No. Identificación</th>
                                        <th class="px-4 py-2">Nombre</th>
                                        <th class="px-4 py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clientes as $clien)
                                        <tr class="text-center" wire:key="{{ $clien->id }}">
                                            <td class="text-white border px-4 uppercase">
                                                {{ $clien->numero_identificacion }}</td>
                                            <td class="text-white border px-4 uppercase">{{ $clien->nombre }}</td>
                                            <td
                                                class="text-white border px-4  flex items-center justify-center gap-2 w-full">
                                                <button wire:click.prevent='asignarCliente({{ $clien }})'
                                                    class="text-2xl px-2 transition-all text-green-600 hover:scale-105 hover:text-green-500 hover:cursor-pointer">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <button wire:click.prevent="cerrarModalClientes"
                        class="text-white px-4 py-1 bg-gray-600 my-2 transition-all hover:scale-105 rounded-sm">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($modalVentas)
        <div
            class="transition-all ease-in fixed z-50 bg-[rgba(0,0,0,0.5)] top-0 left-0 w-full p-2 h-screen overflow-y-scroll backdrop-blur-sm">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-gray-950 p-4 rounded-lg w-full max-w-2xl">

                    <div class="flex items-center bg-white px-2 rounded-sm">
                        <label for="busquedaVenta">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </label>
                        <input wire:model.live="busquedaVenta" type="search" id="busquedaVenta" name="busquedaVenta"
                            placeholder="No. Factura: Ej: 0000002..."
                            class="border-none focus:ring-0 p-2 w-full focus:border-none">
                    </div>

                    <h2 class="text-4xl text-center mb-2 text-white my-4">Lista Ventas</h2>

                    <div class="overflow-x-auto overflow-hidden">
                        @if (count($ventas) == 0)
                            <p class="text-center text-2xl text-white">No se han encontrado clientes</p>
                        @else
                            <!-- Lista de productos -->
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="px-4 py-2">No. Factura</th>
                                        <th class="px-4 py-2">Cliente</th>
                                        <th class="px-4 py-2">Total</th>
                                        <th class="px-4 py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ventas as $venta)
                                        <tr class="text-center" wire:key="{{ $venta->id }}">
                                            <td class="text-white border px-4 uppercase">
                                                {{ $venta->numero_factura }}</td>
                                            <td class="text-white border px-4 uppercase">
                                                {{ $venta->cliente->nombre ?? 'Super Usuario' }}</td>
                                            <td class="text-white border px-4 uppercase">
                                                {{ $this->formatearDinero($venta->total) }}</td>
                                            <td
                                                class="text-white border px-4  flex items-center justify-center gap-2 w-full">

                                                <button wire:click.prevent="generarPDFRecibo({{ $venta->id }})"
                                                    class="text-2xl px-2 transition-all text-green-600 hover:scale-105 hover:text-green-500 hover:cursor-pointer">
                                                    <i class="fa-solid fa-print"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <button wire:click.prevent="cerrarModalVentas"
                        class="text-white px-4 py-1 bg-gray-600 my-2 transition-all hover:scale-105 rounded-sm">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    <h2 class="text-4xl text-center mb-2 font-black text-gray-600 ">Modulo POS</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md min-h-96">
        <div class="fixed bottom-0 left-0 shadow bg-indigo-100 w-full p-4 grid grid-cols-2 gap-4 md:grid-cols-4 z-20">

            <div class="flex flex-col">
                <p class="md:text-xl font-bold text-indigo-800">Total</p>
                <p class="text-2xl lg:text-6xl font-bold text-indigo-800">$
                    <span>{{ $this->formatearDinero($this->total) }}</span>
                </p>
            </div>
            <div class="flex flex-col">
                <label for="recibido" class="md:text-xl font-bold text-green-800">Efectivo Recibido</label>
                <div class="flex items-center text-left text-green-800 p-0 font-bold text-2xl lg:text-5xl">
                    <span>
                        $
                    </span>
                    <input wire:model="recibido" wire:keydown.enter='calcularCambio' type="text" id="recibido"
                        name="recibido" min="0" placeholder="0"
                        class=" border-none focus:ring-0 bg-transparent no-spin-buttons p-0 text-2xl md:text-5xl  overflow-hidden w-full placeholder:text-green-800" />
                </div>
            </div>
            <div class=" flex flex-col">
                <p class="md:text-xl font-bold text-red-600">Cambio</p>
                <p class="text-2xl lg:text-6xl text-red-600 font-bold">$ {{ $this->formatearDinero($this->cambio) }}
                </p>
            </div>
            <div class="flex items-center">
                <button wire:click.prevent='confirmarVenta'
                    class="bg-green-600 w-full p-2 rounded-sm text-white font-bold transition-all hover:scale-105">
                    Realizar Venta (AvPág)
                </button>
            </div>
        </div>

        <div class="mb-4 relative">
            <div class="relative">
                <div class="flex items-center bg-indigo-100 px-2 rounded-sm">
                    <label for="busqueda">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </label>
                    <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda" autofocus
                        placeholder="Producto: Ej: P01 Ej:Postre"
                        class="border-none focus:ring-0 p-2 w-full bg-transparent">
                </div>
                @if ($modalProductos)
                    <div
                        class="flex items-center justify-center absolute top-full w-full z-10 border-t-2 border-white">
                        <div class="bg-indigo-100 p-4 w-full h-96 overflow-hidden overflow-y-scroll ">
                            <h2 class="text-2xl text-center md:text-4xl font-black text-gray-600">
                                {{ 'Listado Productos' }}
                            </h2>
                            <div class="overflow-x-auto">
                                @if (count($productos) == 0)
                                    <p class="text-center text-2xl">No se han encontrado productos</p>
                                @else
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 p-2">
                                        @foreach ($productos as $producto)
                                            <div wire:key='{{ $producto->id }}'
                                                class="shadow-md relative flex flex-col items-center gap-2">
                                                <img loading="lazy"
                                                    src="{{ asset('storage/uploads/imagenes_productos/' . $producto->imagen) }}"
                                                    alt="Imagen del usuario" class="object-cover my-2 h-24 px-2">

                                                <div class="p-2 text-center">
                                                    <p class="capitalize text-xs font-bold">{{ $producto->codigo }}
                                                    </p>
                                                    <p class="capitalize font-bold text-gray-800">
                                                        {{ $producto->nombre }}
                                                    </p>
                                                    <p class="text-xl font-bold text-indigo-800">
                                                        ${{ $this->formatearDinero($producto->precio) }}</p>
                                                    <input id="producto-cantidad"
                                                        class="max-w-14 p-1 m-0 bg-transparent border-gray-200 focus:ring-0 focus:outline-none focus:border-gray-100"
                                                        type="number" wire:model="cantidad.{{ $producto->id }}"
                                                        min="1" max="999999999" value=""
                                                        wire:keydown.enter="agregarProductoCarrito({{ $producto }})">
                                                    <button
                                                        class="text-2xl px-2 transition-all text-green-800 hover:scale-105 hover:text-green-600 hover:cursor-pointer"
                                                        wire:click.prevent="agregarProductoCarrito({{ $producto }})">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    <!-- Lista de productos -->
                                    {{--    <table class="min-w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="px-4 py-2">Código</th>
                                            <th class="px-4 py-2">Nombre</th>
                                            <th class="px-4 py-2">Precio</th>
                                            <th class="px-4 py-2">Cantidad</th>
                                            <th class="px-4 py-2">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($productos as $producto)
                                            <tr class="text-center" wire:key="{{ $producto->id }}">
                                                <td class="border px-4 uppercase">{{ $producto->codigo }}</td>
                                                <td class="border px-4 uppercase">{{ $producto->nombre }}</td>
                                                <td class="border px-4">$
                                                    {{ $this->formatearDinero($producto->precio) }}</td>
                                                <td class="border px-4">
                                                    <input
                                                        class="max-w-14 p-1 m-0 border-gray-200 focus:ring-0 focus:outline-none focus:border-gray-100"
                                                        type="number" value="{{ $cantidad[$producto->id] ?? 1 }}"
                                                        min="1" wire:model="cantidad.{{ $producto->id }}">
                                                </td>
                                                <td class="border px-4 flex items-center justify-center gap-2 w-full">
                                                    <button
                                                        class="text-2xl px-2 transition-all text-gray-800 hover:scale-105 hover:text-green-600 hover:cursor-pointer"
                                                        wire:click.prevent="agregarProductoCarrito({{ $producto }})">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table> --}}
                                @endif
                            </div>
                            <button wire:click.prevent="cerrarModalProductos"
                                class="text-white px-4 bg-gray-600 my-2 transition-all hover:scale-105">Cerrar</button>
                        </div>
                    </div>

                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 md:flex lg:flex justify-center mb-4">
            <button wire:click.prevent='abrirModalClientes'
                class="bg-gray-900 text-sm p-2 font-bold transition-all text-white hover:bg-gray-800  hover:cursor-pointer">
                Asignar Cliente (F2)
            </button>
            <button wire:click.prevent='abrirModalVentas'
                class="bg-green-900 text-sm p-2 font-bold transition-all text-white hover:bg-green-800  hover:cursor-pointer">
                Consultar Ventas (F3)
            </button>
            <button wire:click.prevent='guardarVentaEnMemoria'
                class="bg-red-700 text-sm p-2 font-bold transition-all text-white hover:bg-red-800  hover:cursor-pointer">
                Enviar A Memoria (F4)
            </button>
            <button wire:click.prevent='recuperarVentaDeMemoria'
                class="bg-gray-600 text-sm p-2 font-bold transition-all text-white hover:bg-gray-500  hover:cursor-pointer">
                Traer De Memoria (F6)
            </button>

            @if ($ultimaVenta)
                <a target="_blank" href="{{ route('imprimirUltimaVenta') }}"
                    class="text-center bg-indigo-900 text-sm rounded-sm p-2 font-bold transition-all text-white hover:scale-105  hover:cursor-pointer">
                    Imprimir Última Venta (F8)
                </a>
            @endif
        </div>


        @if (count($carrito) == 0)
            <div class="text-center p-4">
                <i class="fa-solid fa-cart-shopping text-6xl text-indigo-600"></i>
                <p class="text-center text-md md:text-4xl font-bold">¡Aún no has agregado productos al carrito!</p>
            </div>
        @else
            <!-- Lista de productos -->
            <h2 class="text-center font-bold text-2xl">Productos Agregados</h2>

            <div class="overflow-x-auto overflow-hidden">
                <table class="min-w-full table-auto mt-2">
                    <thead class="">
                        <tr class=" bg-gray-200">
                            <th class="p-2">Código</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Precio</th>
                            <th class="p-2">Cantidad</th>
                            <th class="p-2  ">SubTotal</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="w-full overflow-x-scroll text-center">
                        @foreach ($carrito as $producto)
                            <tr class="border hover:bg-indigo-100" wire:key="{{ $producto->id }}">
                                <td class="px-2 border uppercase ">{{ $producto->attributes->codigo }}
                                </td>
                                <td class="px-2 border capitalize">{{ $producto->name }}</td>
                                <td class="px-2 border  ">$ {{ $this->formatearDinero($producto->price) }}
                                </td>
                                <td class="px-2 border">
                                    <input wire:keydown.enter='actualizarCantidad({{ $producto->id }})'
                                        wire:model="carrito.{{ $producto->id }}.quantity"
                                        class="px-2 max-w-16 bg-transparent border-gray-200 focus:ring-0 focus:outline-none focus:border-gray-100"
                                        type="number" min="1" max="999999999" value=""/>
                                    <button
                                        class="px-2 text-lg transition-all text-gray-800 hover:scale-105 hover:text-green-600 hover:cursor-pointer"
                                        wire:click.prevent="actualizarCantidad({{ $producto->id }})">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </td>
                                <td class="px-2 border">${{ $producto->subtotal }}</td>
                                <td class="px-2 border">
                                    <button
                                        class="bg-red-500 text-lg px-2 py-1 transition-all text-white hover:cursor-pointer"
                                        wire:click.prevent="eliminarProductoCarrito({{ $producto->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button wire:click.prevent="vaciarCarrito"
                class="text-white text-sm rounded-sm p-2 bg-red-600 my-2 font-bold transition-all hover:scale-105"><i
                    class="fa-solid fa-trash mr-1"></i>Reiniciar Venta (Supr)</button>
        @endif
    </div>
</div>


@push('scripts')
    <script>
        Livewire.on("noHayProductos", () => {
            Swal.fire({
                title: "La venta esta llegando vacia",
                text: "Toca mirar bien.",
                icon: "warning"
            });
        });

        function imprimirRecibo() {
            Livewire.dispatch('cerrarModalRecibo');
        }

        Livewire.on("ventaCreada", () => {
            Swal.fire({
                title: "Venta Creada",
                text: "Venta creada correctamente",
                icon: "success"
            });
        });
        Livewire.on("ErrorRecibido", () => {
            Swal.fire({
                title: "Error",
                text: "Ell valor ",
                icon: "error"
            });
        });
        Livewire.on("errorCambio", () => {
            Swal.fire({
                title: "Error Dinero Recibido",
                text: "El dinero recibido no puede ser menor al total.",
                icon: "error"
            });
        });
        Livewire.on("carritoVacio", () => {
            Swal.fire({
                title: "Carrito Vacío",
                text: "El carrito no puede estar vacío.",
                icon: "warning"
            });
        });
        Livewire.on("errorVenta", () => {
            Swal.fire({
                title: "Error Venta",
                text: "Ha ocurrido un error en la venta.",
                icon: "warning"
            });
        });
        Livewire.on("errorCantidadProducto", () => {
            Swal.fire({
                title: "Error Cantidad Productos",
                text: "La cantidad solicitada excede el total en el inventario.",
                icon: "warning"
            });
        });
        Livewire.on("noHayVentasEnMemoria", () => {
            Swal.fire({
                title: "Error Ventas Memoria",
                text: "Aún no hay ventas en memoria.",
                icon: "warning"
            });
        });
        Livewire.on("enviarAMemoria", () => {
            Swal.fire({
                title: "Enviado a memoria",
                text: "Venta enviada a memoria.",
                icon: "success"
            });
        });
        Livewire.on("recuperarDeMemoria", () => {
            Swal.fire({
                title: "Recuperar de memoria",
                text: "Venta recuperada a memoria.",
                icon: "success"
            });
        });
        Livewire.on('confirmarVenta', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas confirmar la venta?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('realizarVenta'); // Emitir evento para proceder con la venta
                }
            });
        });
    </script>
@endpush
