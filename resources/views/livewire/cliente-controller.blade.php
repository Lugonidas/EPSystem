<div class="md:p-6 relative" wire:keydown.window='teclaPresionada($event.key)'>
    <h2 class="text-4xl text-center font-black mb-2 text-gray-600 ">Clientes</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md">
        <div class="flex flex-col md:flex-row my-2">
            <x-secondary-button wire:click.prevent="abrirModalAgregar"
                class="bg-gray-700 rounded-none hover:bg-indigo-800 hover:text-white">
                <i class="fa-solid fa-plus"></i> Cliente (F2)
            </x-secondary-button>
            <x-nav-link href="{{ route('generarPDFClientes') }}" target="_blank" class="bg-red-700 hover:bg-red-800">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </x-nav-link>
            <x-nav-link href="{{ route('generarExcelClientes') }}" target="_blank"
                class="bg-green-700 hover:bg-green-800">
                <i class="fa-solid fa-file-csv"></i> EXCEL
            </x-nav-link>
        </div>

        <div class="mb-4 flex items-center bg-indigo-100 px-2">
            <label for="busqueda">
                <i class="fa-solid fa-magnifying-glass"></i>
            </label>
            <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda" placeholder="Ej: 1073..."
                autofocus class="border-none focus:ring-0 p-2 w-full bg-transparent">
        </div>

        @if (count($clientes) === 0)
            <p class="text-2xl text-center font-bold">Aún no has agregado clientes</p>
        @else
            <!-- Lista de clientes -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">No. Identificación</th>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Celular</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr class="clienteId text-center hover:bg-indigo-100 hover:cursor-pointer"
                                wire:key='{{ $cliente->id }}'>
                                <td class="border px-4">{{ $cliente->numero_identificacion }}</td>
                                <td class="border px-4 capitalize">{{ $cliente->nombre }}</td>
                                <td class="border px-4">{{ $cliente->celular }}</td>
                                <td class="border px-4 flex items-center justify-center w-full">
                                    <div class="flex gap-1 justify-center">
                                        <button class="bg-indigo-600 text-lg px-2 py-1 text-white hover:cursor-pointer"
                                            wire:click.prevent="abrirModalEditar({{ $cliente->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button
                                            wire:click.prevent="$dispatch('confirmarEliminarCliente', { clienteId: {{ $cliente->id }} })"
                                            class="bg-red-500 text-lg px-2 py-1 text-white hover:cursor-pointer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif

        <!-- Modal para agregar usuario -->
        @if ($modalAgregar || $modalEditar)
            <div wire:transition.opacity.duration.400ms
                class="transition-all ease-in fixed bg-[rgba(0,0,0,0.5)] backdrop-blur top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-4 rounded-lg w-full max-w-2xl">
                        <!-- Contenido del modal para agregar usuario -->
                        <form wire:submit.prevent="{{ $modalAgregar ? 'crearCliente' : 'actualizarCliente' }}"
                            enctype="multipart/form-data">
                            <legend class="text-2xl font-bold text-gray-600 text-center mb-2">
                                {{ $modalAgregar ? 'Agregar Cliente' : 'Actualizar Cliente' }}</legend>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="mb-2">
                                    @if ($modalAgregar)
                                        @if ($imagen)
                                            <img src="{{ $imagen->temporaryUrl() }}" alt="Imagen previa"
                                                class="object-cover mb-2 w-24" />
                                        @endif
                                        <x-input-label class="text-gray-600" for="imagen"
                                            :value="__('Imagen')" />
                                        <x-text-input wire:model="imagen" id="imagen"
                                            class="border-none text-xs p-0 block mt-1 w-full capitalize text-white"
                                            type="file" name="imagen" accept="image/*" />
                                        <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                                    @else
                                        @if ($imagen)
                                            <img src="{{ asset('storage/uploads/imagenes_clientes/' . $imagen) }}"
                                                alt="Imagen previa" class="object-cover mb-2 w-24" />
                                        @endif
                                        <x-input-label class="text-gray-600" for="imagen"
                                            :value="__('Imagen')" />
                                        <x-text-input wire:model="imagen_nueva" id="imagen_nueva"
                                            class="border-none text-xs p-0 block mt-1 w-full capitalize text-white"
                                            type="file" name="imagen" accept="image/*" />
                                        <x-input-error :messages="$errors->get('imagen_nueva')" class="mt-2" />
                                    @endif
                                </div>

                                <div class="mb-2">
                                </div>

                                <div class="mb-2">
                                    <x-input-label class="hidden" for="numero_identificacion" :value="__('No. Identiifación')" />
                                    <x-text-input placeholder="Número de identificación" wire:model="numero_identificacion" id="numero_identificacion"
                                        class="block mt-1 w-full" type="text" name="numero_identificacion"
                                        autocomplete="numero_identificacion" />
                                    <x-input-error :messages="$errors->get('numero_identificacion')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="nombre" :value="__('Nombre')" />
                                    <x-text-input placeholder="Nombre completo" wire:model="nombre" id="nombre"
                                        class="block mt-1 w-full capitalize" type="text" name="nombre"
                                        autocomplete="nombre" />
                                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="email" :value="__('Correo')" />
                                    <x-text-input placeholder="Email" wire:model="email" id="email" class="block mt-1 w-full"
                                        type="email" name="email" autocomplete="email" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="celular" :value="__('Celular')" />
                                    <x-text-input placeholder="Número de contacto" wire:model="celular" id="celular" class="block mt-1 w-full"
                                        type="tel" name="celular" autocomplete="celular" />
                                    <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="ciudad" :value="__('Ciudad')" />
                                    <x-text-input placeholder="Ciudad" wire:model="ciudad" id="ciudad"
                                        class="block mt-1 w-full capitalize" type="text" name="ciudad"
                                        autocomplete="ciudad" />
                                    <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="barrio" :value="__('Barrio')" />
                                    <x-text-input placeholder="Barrio" wire:model="barrio" id="barrio"
                                        class="block mt-1 w-full capitalize" type="text" name="barrio"
                                        autocomplete="barrio" />
                                    <x-input-error :messages="$errors->get('barrio')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="direccion" :value="__('Direccion')" />
                                    <x-text-input placeholder="Dirección" wire:model="direccion" id="direccion"
                                        class="block mt-1 w-full capitalize" type="text" name="direccion"
                                        autocomplete="direccion" />
                                    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-secondary-button type="submit" wire:loading.attr="disabled"
                                    class="px-4 text-indigo-800 hover:bg-indigo-800 hover:text-white border-indigo-800">
                                    {{ $modalAgregar ? 'Agregar' : 'Guardar' }}
                                </x-secondary-button>
                                <x-secondary-button
                                    wire:click.prevent="{{ $modalAgregar ? 'cerrarModalAgregar' : 'cerrarModalEditar' }}"
                                    class="px-4 hover:bg-gray-600 hover:text-white border-gray-600 text-gray-600">
                                    Cancelar
                                </x-secondary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("livewire:init", function() {
            Livewire.on("confirmarEliminarCliente", clienteId => {
                Swal.fire({
                    title: "¿Está seguro?",
                    text: "Esta acción no se puede revertir!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch("eliminarCliente", clienteId);
                    }
                });
            });

            Livewire.on("clienteEliminado", () => {
                Swal.fire({
                    title: "Cliente Eliminado",
                    text: "El cliente ha sido eliminado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("clienteCreado", () => {
                Swal.fire({
                    title: "Cliente Creado",
                    text: "El cliente se ha creado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("clienteActualizado", () => {
                Swal.fire({
                    title: "Cliente Actualizado",
                    text: "El cliente se ha actualizado correctamente.",
                    icon: "success"
                });
            });

            let clientes = document.querySelectorAll(".clienteId");

            clientes.forEach(cliente => {
                cliente.addEventListener("dblclick", e => {
                    e.preventDefault();
                    let clienteId = e.currentTarget.getAttribute('wire:key');
                    Livewire.dispatch("abrirModalEditar", {
                        id: clienteId
                    });
                });
            });
        });
    </script>
@endpush
