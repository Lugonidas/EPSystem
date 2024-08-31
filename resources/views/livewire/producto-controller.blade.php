<div class="md:p-6 relative" wire:keydown.window='teclaPresionada($event.key)'>
    <h2 class="text-4xl text-center font-black text-gray-600  mb-2">Productos</h2>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md">
        <div class="flex flex-col md:flex-row my-2">
            <x-secondary-button wire:click.prevent="abrirModalAgregar"
                class="bg-gray-700 rounded-none hover:bg-indigo-800 hover:text-white">
                <i class="fa-solid fa-plus"></i> Producto (F2)
            </x-secondary-button>
            <x-nav-link href="{{ route('generarPDFProductos') }}" target="_blank" class="bg-red-700 hover:bg-red-800">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </x-nav-link>
            <x-nav-link href="{{ route('generarExcelProductos') }}" target="_blank"
                class="bg-green-700 hover:bg-green-800">
                <i class="fa-solid fa-file-csv"></i> EXCEL
            </x-nav-link>
        </div>

        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div class="flex items-center bg-indigo-100 px-2">
                <label for="busqueda">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </label>
                <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda"
                    placeholder="Ej: P01 Ej:Pera" class="border-none focus:ring-0 p-2 w-full bg-transparent" autofocus>

            </div>
            <div class="grid md:grid-cols-2 items-center gap-2">
                <label for="filtro" class="font-bold md:text-xl md:text-right">Filtrar por categoría</label>
                <select wire:model.live="filtro" id="filtro" name="filtro"
                    class="p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                    <option value="">Todos</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (count($productos) == 0)
            <p class="text-2xl text-center font-bold">Aún no has creado productos</p>
        @else
            <!-- Lista de productos -->
            <div class="overflow-x-auto overflow-hidden">
                <table class="min-w-full table-auto" id="tablaProductos">
                    <thead>
                        <tr class="">
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Código</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Imagen</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Nombre</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Precio</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Cantidad</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Estado</th>
                            <th class="border border-gray-300 px-4 py-2 text-indigo-800">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr class="productoId hover:cursor-pointer text-center transition-all hover:bg-indigo-100 {{ $producto->stock < 10 ? 'bg-red-400' : '' }}"
                                wire:key='{{ $producto->id }}'>
                                <td class="border px-4 uppercase">{{ $producto->codigo }}</td>
                                <td class="border px-4">
                                    <img loading="lazy"
                                        src="{{ asset('storage/uploads/imagenes_productos/' . $producto->imagen) }}"
                                        alt="Imagen del usuario" class="object-cover my-2 w-16 mx-auto" />
                                </td>
                                <td class="border px-4 capitalize">{{ $producto->nombre }}</td>
                                <td class="border px-4">$ {{ $this->formatearDinero($producto->precio) }}</td>
                                <td class="border px-4">
                                    {{ $producto->stock }}</td>
                                <td class="border px-4">
                                    <button wire:click.prevent="cambiarEstado({{ $producto->id }})"
                                        class="focus:outline-none text-2xl"
                                        title="{{ $producto->estado == 1 ? 'Desactivar' : 'Activar' }}">
                                        <i
                                            class="fa-solid {{ $producto->estado == 1 ? 'fa-check text-green-500' : 'fa-xmark text-red-500' }}"></i>
                                    </button>
                                </td>
                                <td class="border px-4">
                                    <div class="flex gap-1 w-full justify-center">
                                        <button class="bg-indigo-600 text-lg px-2 py-1 text-white hover:cursor-pointer"
                                            wire:click.prevent="abrirModalEditar({{ $producto->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="submit"
                                            wire:click.prevent="$dispatch('confirmarEliminarProducto',{ productoId: {{ $producto->id }} })"
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

        <!-- Modal para agregar o editar producto -->
        @if ($modalAgregar || $modalEditar)
            <div class="fixed bg-[rgba(0,0,0,0.5)] backdrop top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll" wire:click.self="{{ $modalAgregar ? 'cerrarModalAgregar' : 'cerrarModalEditar' }}">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-4 rounded-sm w-full max-w-2xl">
                        <!-- Contenido del modal para agregar producto -->
                        <form wire:submit.prevent="{{ $modalAgregar ? 'crearProducto' : 'actualizarProducto' }}"
                            enctype="multipart/form-data">
                            <legend class="text-2xl text-center text-gray-600 font-black mb-2">
                                {{ $modalAgregar ? 'Agregar Producto' : 'Actualizar Producto' }}</legend>
                            <div class="grid grid-cols-2 gap-2">

                                <div class="mb-2">
                                    @if ($modalAgregar)
                                        @if ($imagen)
                                            <img loading="lazy" src="{{ $imagen->temporaryUrl() }}" alt="Imagen previa"
                                                class="object-cover mb-2 w-24" />
                                        @endif
                                        <x-input-label class="hidden" class="text-white" for="imagen"
                                            :value="__('Imagen')" />
                                        <x-text-input wire:model="imagen" id="imagen"
                                            class="border-none text-xs p-0 block mt-1 w-full capitalize text-white"
                                            type="file" name="imagen" accept="image/*" />
                                        <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                                    @else
                                        @if ($imagen)
                                            <img loading="lazy" src="{{ asset('storage/uploads/imagenes_productos/' . $imagen) }}"
                                                alt="Imagen previa" class="object-cover mb-2 w-24" />
                                        @endif
                                        <x-input-label class="hidden" class="text-white" for="imagen"
                                            :value="__('Imagen')" />
                                        <x-text-input placeholder="" wire:model="imagen_nueva" id="imagen_nueva"
                                            class="border-none text-xs p-0 block mt-1 w-full capitalize text-white"
                                            type="file" name="imagen" accept="image/*" />
                                        <x-input-error :messages="$errors->get('imagen_nueva')" class="mt-2" />

                                    @endif
                                </div>

                                <div class="mb-2">
                                </div>

                                <div class="mb-2">
                                    <x-input-label class="hidden" for="nombre" :value="__('Nombre')" />
                                    <x-text-input placeholder="Nombre Completo" wire:model="nombre" id="nombre"
                                        class="block mt-1 w-full capitalize" type="text" name="nombre"
                                        autocomplete="nombre" />
                                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="codigo" :value="__('Código')" />
                                    <x-text-input placeholder="Código" wire:model="codigo" id="codigo"
                                        class="block mt-1 w-full uppercase" type="text" name="codigo"
                                        autocomplete="codigo" />
                                    <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="precio" :value="__('Precio')" />
                                    <x-text-input placeholder="Precio" wire:model.lazy="precio" id="precio"
                                        class="block mt-1 w-full" type="text" name="precio" 
                                        autocomplete="precio" />
                                    <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="stock" :value="__('Cantidad')" />
                                    <x-text-input placeholder="Cantidad" wire:model="stock" id="stock"
                                        class="block mt-1 w-full" type="text" name="stock"
                                        autocomplete="stock" />
                                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="categoria_id" :value="__('Categoria')" />
                                    <select wire:model="categoria_id" id="categoria_id" name="categoria_id"
                                        class=" text-gray-600 p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                                        <option selected>--- Categoria ---</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="proveedor_id" :value="__('Proveedor')" />
                                    <select wire:model="proveedor_id" id="proveedor_id" name="proveedor_id"
                                        class="text-gray-600 p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                                        <option selected>--- Proveedor ---</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('proveedor_id')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="impuesto" :value="__('Impuesto')" />
                                    <x-text-input placeholder="Impuesto" wire:model="impuesto" id="impuesto"
                                        class="block mt-1 w-full" type="text" name="impuesto"
                                        autocomplete="impuesto" value="$impuesto" />
                                    <x-input-error :messages="$errors->get('impuesto')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="unidad_medida" :value="__('Unidad Medida')" />
                                    <x-text-input placeholder="Unidad de medida" wire:model="unidad_medida"
                                        id="unidad_medida" class="block mt-1 w-full capitalize text-gray-600"
                                        type="text" name="unidad_medida" autocomplete="unidad_medida" />
                                    <x-input-error :messages="$errors->get('unidad_medida')" class="mt-2" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label class="hidden" for="estado" :value="__('Estado')" />
                                    <select wire:model="estado" id="estado" name="estado"
                                        class=" text-gray-600 p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
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
            Livewire.on("confirmarEliminarProducto", productoId => {
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
                        Livewire.dispatch("eliminarProducto", productoId);
                    }
                });
            });

            Livewire.on("productoEliminado", () => {
                Swal.fire({
                    title: "Producto Eliminado",
                    text: "El producto ha sido eliminado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("productoCreado", () => {
                Swal.fire({
                    title: "Producto Creado",
                    text: "El producto se ha creado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("productoActualizado", () => {
                Swal.fire({
                    title: "Producto Actualizado",
                    text: "El producto se ha actualizado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("productoErrorEliminar", () => {
                Swal.fire({
                    title: "Error",
                    text: "No puede eliminar el producto porque esta relacoinado en las ventas.",
                    icon: "error"
                });
            });

            Livewire.on("estadoCambiado", () => {
                Swal.fire({
                    title: "Estado Actualizado",
                    text: "El estado ha sido actualizado correctamente.",
                    icon: "success"
                });
            });

            let productos = document.querySelectorAll(".productoId");

            productos.forEach(producto => {
                producto.addEventListener("dblclick", e => {
                    e.preventDefault();
                    let productoId = e.currentTarget.getAttribute('wire:key');
                    Livewire.dispatch("abrirModalEditar", {
                        id: productoId
                    });
                });
            });
        });
    </script>
@endpush
