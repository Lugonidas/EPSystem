<div class="p-4 relative" wire:keydown.window='teclaPresionada($event.key)'>
    <h2 class="text-4xl text-center font-black text-gray-600  mb-2">Categorías</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md">
        <div class="flex flex-col md:flex-row my-2">
            <x-secondary-button wire:click.prevent="abrirModalAgregar"
                class="bg-gray-700 rounded-none hover:bg-indigo-800 hover:text-white">
                <i class="fa-solid fa-plus"></i> Categoria (F2)
            </x-secondary-button>
            <x-nav-link href="{{ route('generarPDFCategorias') }}" target="_blank" class="bg-red-700 hover:bg-red-800">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </x-nav-link>
            <x-nav-link href="{{ route('generarExcelCategorias') }}" target="_blank"
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

        @if (count($categorias) === 0)
            <p class="text-2xl text-center font-bold">Aún no has agreado categorías.</p>
        @else
            <!-- Lista de usuarios -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr class="categoriaId text-center hover:bg-indigo-100 hover:cursor-pointer"
                                wire:key='{{ $categoria['id'] }}'>
                                <td class="border px-4 capitalize">{{ $categoria['nombre'] }}</td>
                                <td class="border px-4 capitalize">
                                    <button wire:click.prevent="cambiarEstado({{ $categoria->id }})"
                                        class="focus:outline-none text-2xl"
                                        title="{{ $categoria->estado === 1 ? 'Desactivar' : 'Activar' }}">
                                        <i
                                            class="fa-solid {{ $categoria->estado === 1 ? 'fa-check text-green-500' : 'fa-xmark text-red-500' }}"></i>
                                    </button>
                                </td>
                                <td class="border px-4 flex items-center justify-center w-full">
                                    <div class="flex gap-1 justify-center">
                                        <button class="bg-indigo-600 text-lg px-2 py-1 text-white hover:cursor-pointer"
                                            wire:click.prevent="abrirModalEditar({{ $categoria['id'] }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button
                                            wire:click.prevent="$dispatch('confirmarEliminarCategoria', { categoria_id: {{ $categoria['id'] }} })"
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

        <!-- Modal para agregar categoria -->
        @if ($modalAgregar || $modalEditar)
            <div wire:transition.opacity.duration.400ms
                class="transition-all ease-in fixed bg-[rgba(0,0,0,0.5)] backdrop-blur top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-4 rounded-lg w-full max-w-2xl">
                        <form wire:submit.prevent="{{ $modalAgregar ? 'crearCategoria' : 'actualizarCategoria' }}"
                            enctype="multipart/form-data">
                            <legend class="text-2xl font-bold text-gray-600 text-center mb-2">
                                {{ $modalAgregar ? 'Agregar Categoria' : 'Actualizar Categoria' }}</legend>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="nombre" :value="__('Nombre')" />
                                    <x-text-input placeholder="Nombre" wire:model="nombre" id="nombre"
                                        class="block mt-1 w-full text-gray-600 capitalize" type="text" name="nombre"
                                        autocomplete="nombre" />
                                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="estado" :value="__('Estado')" />
                                    <select wire:model="estado" id="estado" name="estado"
                                        class="p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full text-gray-600 capitalize">
                                        <option selected>--- Estado ---</option>
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
            Livewire.on("confirmarEliminarCategoria", categoriaId => {
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
                        Livewire.dispatch("eliminarCategoria", categoriaId);
                    }
                });
            });

            Livewire.on("categoriaEliminada", () => {
                Swal.fire({
                    title: "Categoría Eliminada",
                    text: "La categoría ha sido eliminado correctamente.",
                    icon: "success"
                });
            });
            Livewire.on("categoriaError", () => {
                Swal.fire({
                    title: "Error",
                    text: "La categoría tiene productos asociados.",
                    icon: "error"
                });
            });

            Livewire.on("categoriaCreada", () => {
                Swal.fire({
                    title: "Categoría Creada",
                    text: "El usuario se ha creado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("categoriaActualizada", () => {
                Swal.fire({
                    title: "Categoría Actualizada",
                    text: "La categoría se ha actualizado correctamente.",
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

            let categorias = document.querySelectorAll(".categoriaId");


            categorias.forEach(categoria => {
                categoria.addEventListener("dblclick", e => {
                    e.preventDefault();
                    let categoriaId = e.currentTarget.getAttribute('wire:key');
                    Livewire.dispatch("abrirModalEditar", {
                        id: categoriaId
                    });
                });
            });
        });
    </script>
@endpush
