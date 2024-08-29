<div class="p-4 relative" wire:keydown.window='teclaPresionada($event.key)'>
    <h2 class="text-4xl text-center mb-2 font-black text-gray-600 ">Proveedores</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md">
        <div class="flex flex-col md:flex-row my-2">
            <x-secondary-button wire:click.prevent="abrirModalAgregar"
                class="bg-gray-700 rounded-none hover:bg-indigo-800 hover:text-white">
                <i class="fa-solid fa-plus"></i> Proveedor (F2)
            </x-secondary-button>
            <x-nav-link href="{{ route('generarPDFProveedores') }}" target="_blank" class="bg-red-700 hover:bg-red-800">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </x-nav-link>
            <x-nav-link href="{{ route('generarExcelProveedores') }}" target="_blank"
                class="bg-green-700 hover:bg-green-800">
                <i class="fa-solid fa-file-csv"></i> EXCEL
            </x-nav-link>
        </div>

        <div class="mb-4 flex items-center bg-indigo-100 px-2">
            <label for="busqueda">
                <i class="fa-solid fa-magnifying-glass"></i>
            </label>
            <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda" placeholder="Ej: Rosa"
                autofocus class="border-none focus:ring-0 p-2 w-full bg-transparent">
        </div>

        @if (count($proveedores) === 0)
            <p class="text-2xl text-center font-bold">Aún no has agreado proveedores.</p>
        @else
            <!-- Lista de usuarios -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Celular</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proveedores as $proveedor)
                            <tr class="proveedorId text-center hover:bg-indigo-100 hover:cursor-pointer"
                                wire:key='{{ $proveedor->id }}'>
                                <td class="border px-4 capitalize">{{ $proveedor->nombre }}</td>
                                <td class="border px-4 capitalize">{{ $proveedor->email }}</td>
                                <td class="border px-4 capitalize">{{ $proveedor->celular }}</td>
                                <td class="border px-4 capitalize">
                                    <button wire:click.prevent="cambiarEstado({{ $proveedor->id }})"
                                        class="focus:outline-none text-2xl"
                                        title="{{ $proveedor->estado === 1 ? 'Desactivar' : 'Activar' }}">
                                        <i
                                            class="fa-solid {{ $proveedor->estado === 1 ? 'fa-check text-green-500' : 'fa-xmark text-red-500' }}"></i>
                                    </button>
                                </td>
                                <td class="border px-4 flex items-center justify-center w-full">
                                    <div class="flex gap-1 justify-center">
                                        <button class="bg-indigo-600 text-lg px-2 py-1 text-white hover:cursor-pointer"
                                            wire:click.prevent="abrirModalEditar({{ $proveedor->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button
                                            wire:click.prevent="$dispatch('confirmarEliminarProveedor', { proveedor_id: {{ $proveedor->id }} })"
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

        <!-- Modal para agregar proveedor -->
        @if ($modalAgregar || $modalEditar)
            <div wire:transition.opacity.duration.400ms
                class="transition-all ease-in fixed bg-[rgba(0,0,0,0.5)] backdrop-blur top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-4 rounded-lg w-full max-w-2xl">
                        <!-- Contenido del modal para agregar categoria -->
                        <form wire:submit.prevent="{{ $modalAgregar ? 'crearProveedor' : 'actualizarProveedor' }}" enctype="multipart/form-data">
                            <legend class="text-2xl font-bold text-gray-600 text-center mb-2">Nuevo Proveedor</legend>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="mb-2">
                                    <x-input-label for="nombre" :value="__('Nombre')" />
                                    <x-text-input placeholder="Nombre completo" wire:model="nombre" id="nombre"
                                        class="block mt-1 w-full capitalize" type="text" name="nombre"
                                        autocomplete="nombre" />
                                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input placeholder="Correo electrónico" wire:model="email" id="email" class="block mt-1 w-full capitalize"
                                        type="email" name="email" autocomplete="email" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="celular" :value="__('Celular')" />
                                    <x-text-input placeholder="Número de contacto" wire:model="celular" id="celular"
                                        class="block mt-1 w-full capitalize" type="tel" name="celular"
                                        autocomplete="celular" />
                                    <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="estado" :value="__('Estado')" />
                                    <select wire:model="estado" id="estado" name="estado"
                                        class="p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
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
            Livewire.on("confirmarEliminarProveedor", proveedorId => {
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
                        Livewire.dispatch("eliminarProveedor", proveedorId);
                    }
                });
            });

            Livewire.on("proveedorEliminado", () => {
                Swal.fire({
                    title: "Proveedor Eliminado",
                    text: "El proveedor ha sido eliminado correctamente.",
                    icon: "success"
                });
            });
            Livewire.on("proveedorError", () => {
                Swal.fire({
                    title: "Error",
                    text: "El proveedor tiene productos asociados.",
                    icon: "error"
                });
            });

            Livewire.on("proveedorCreado", () => {
                Swal.fire({
                    title: "Proveedor Creado",
                    text: "El proveedor se ha creado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("proveedorActualizado", () => {
                Swal.fire({
                    title: "Proveedor Actualizado",
                    text: "El proveedor se ha actualizado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("estadoCambiado", () => {
                Swal.fire({
                    title: "Estado Actualizado",
                    text: "El estado ha sido actualizado correctamente.",
                    icon: "success"
                });
            });

            let proveedores = document.querySelectorAll(".proveedorId");

            proveedores.forEach(proveedor => {
                console.log(proveedor)
                proveedor.addEventListener("dblclick", e => {
                    e.preventDefault();
                    let proveedorId = e.currentTarget.getAttribute('wire:key');
                    Livewire.dispatch("abrirModalEditar", {
                        id: proveedorId
                    });
                });
            });
        });
    </script>
@endpush
