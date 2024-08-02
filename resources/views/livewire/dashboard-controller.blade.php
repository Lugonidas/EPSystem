<div class="md:p-6">
    <h2 class="text-4xl text-center font-black mb-2">Dashboard</h2>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md dark:border-gray-700">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div
                class="shadow-md flex flex-col p-2 items-center  justify-center h-24 rounded bg-gray-50 dark:bg-gray-800">
                <p class="font-bold">Última venta <i class="fa-solid fa-chart-simple text-indigo-800"></i></p>
                <p class=" text-xl sm:text-4xl font-bold text-indigo-800">
                    $ {{ $this->formatearDinero($totalUltimaVenta) }}
                </p>
            </div>
            <div
                class="shadow-md flex flex-col p-2 items-center justify-center h-24 rounded bg-gray-50 dark:bg-gray-800">
                <p class="font-bold">Venta Diaria <i class="fa-solid fa-chart-simple text-green-800"></i></p>
                <p class=" text-xl sm:text-4xl font-bold text-indigo-800">
                    $ {{ $this->formatearDinero($totalVentasDelDia) }}
                </p>
            </div>
            <div
                class="shadow-md flex flex-col p-2 items-center justify-center h-24 rounded bg-gray-50 dark:bg-yellow-400-800">
                <p class="font-bold">Venta Semanal <i class="fa-solid fa-chart-simple text-yellow-400"></i></p>
                <p class=" text-xl sm:text-4xl font-bold text-indigo-800">
                    $ {{ $this->formatearDinero($totalVentasDeLaSemana) }}
                </p>
            </div>
            <div
                class="shadow-md flex flex-col p-2 items-center justify-center h-24 rounded bg-gray-50 dark:bg-yellow-400-800">
                <p class="font-bold">Venta Mensual <i class="fa-solid fa-chart-simple text-yellow-400"></i></p>
                <p class=" text-xl sm:text-4xl font-bold text-indigo-800">
                    $ {{ $this->formatearDinero($this->totalVentasDelMes) }}
                </p>
            </div>
        </div>
        <div class="shadow-md flex items-center justify-center mb-4 rounded p-4 bg-indigo-100 dark:bg-gray-800">
            <div class="w-full flex flex-col items-center justify-center rounded ">
                <p class="font-bold uppercase text-center flex items-center gap-2 text-md md:text-2xl text-indigo-600">Productos <i
                        class="fa-solid fa-plus text-yellow-400 text-2xl"></i> Vendidos </p>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-start p-4">
                    @foreach ($productosMasVendidos as $producto)
                        <div class="shadow-md p-4 bg-white rounded-sm capitalize"
                            wire:key='{{ $producto->producto->id }}'>
                            <img src="{{ asset('storage/uploads/imagenes_productos/' . $producto->producto->imagen) }}"
                                alt="Imagen del usuario" class="object-cover my-2 w-12 mx-auto">
                            <p class="text-xs font-bold text-indigo-800">Código: <span class="uppercase">{{ $producto->producto->codigo }}</span>
                            </p>
                            <p class="text-sm">Nombre: {{ $producto->producto->nombre }}</p>
                            <p class="text-sm">Total Vendido: {{ $producto->total_vendido }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="hidden sm:grid md:grid-cols-2 w-full gap-4 p-2">

                    <div class="">
                        <canvas id="productosMasVendidos"></canvas>
                    </div>
                    <div>
                        <canvas id="comportamientoVentas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="shadow-md md:flex items-center justify-center mb-4 rounded p-4 bg-gray-50 dark:bg-gray-800">
            <div class=" flex flex-col items-center justify-center rounded bg-gray-50">
                <p class="font-bold text-center flex items-center gap-2">Clientes Que <i
                        class="fa-solid fa-plus text-yellow-400 text-2xl"></i> Compran </p>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 items-start p-4">
                    @foreach ($this->clientesQueMasCompran as $cliente)
                        <div class="shadow-md p-4 bg-gray-200 rounded-sm capitalize" wire:key='{{ $cliente->id }}'>
                            <img src="{{ asset('storage/uploads/imagenes_clientes/' . $cliente->imagen) }}"
                                alt="Imagen del usuario" class="object-cover my-2 w-12 mx-auto">
                            <p class="text-sm">Nombre: {{ $cliente->nombre }}</p>
                            <p class="text-sm">No. Contacto: {{ $cliente->celular }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col items-center justify-center rounded bg-gray-50">
                <div id="hora-actual"
                    class="shadow-md p-4 bg-indigo-600 text-white font-black text-4xl rounded-sm capitalize">
                    {{ $this->horaActual }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let horaActual = document.querySelector("#hora-actual");
            if (horaActual) {
                setInterval(function() {
                    horaActual.textContent = new Date().toLocaleTimeString();
                }, 1000);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('productosMasVendidos').getContext('2d');
            var productosMasVendidosData = @json($productosMasVendidos); // Assuming data from your controller

            var nombresProductos = productosMasVendidosData.map(function(producto) {
                return producto.producto.nombre;
            });

            var cantidadesVendidas = productosMasVendidosData.map(function(producto) {
                return producto.total_vendido;
            });

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nombresProductos,
                    datasets: [{
                        label: 'Productos más vendidos',
                        data: cantidadesVendidas,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {}
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('comportamientoVentas').getContext('2d');
            var comportamientoVentasData = @json($comportamientoVentas); // Datos de comportamiento de ventas

            var fechas = comportamientoVentasData.map(function(venta) {
                return venta.fecha; // Ajusta esto según la estructura de tus datos
            });

            var ventas = comportamientoVentasData.map(function(venta) {
                return venta.ventas; // Ajusta esto según la estructura de tus datos
            });

            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: [{
                        label: 'Ventas',
                        data: ventas,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
