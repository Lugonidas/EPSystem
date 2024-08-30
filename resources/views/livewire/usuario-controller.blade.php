<div class="md:p-6 relative" wire:keydown.window='teclaPresionada($event.key)'>
    <h2 class="text-4xl text-center font-black mb-2 text-gray-600 ">Empleados</h2>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-md">
        <div class="flex flex-col md:flex-row my-2">
            <x-secondary-button wire:click.prevent="abrirModalAgregar"
                class="bg-gray-700 rounded-none hover:bg-indigo-800 hover:text-white">
                <i class="fa-solid fa-plus"></i> Empleado (F2)
            </x-secondary-button>
            <x-nav-link href="{{ route('generarPDFUsuarios') }}" target="_blank" class="bg-red-700 hover:bg-red-800">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </x-nav-link>
            <x-nav-link href="{{ route('generarExcelUsuarios') }}" target="_blank"
                class="bg-green-700 hover:bg-green-800">
                <i class="fa-solid fa-file-csv"></i> EXCEL
            </x-nav-link>
        </div>

        <div class="mb-4 flex items-center bg-indigo-100 px-2">
            <label for="busqueda" class="sr-only">Buscar</label>
            <i class="fa-solid fa-magnifying-glass"></i>
            <input wire:model.live="busqueda" type="search" id="busqueda" name="busqueda" placeholder="Ej: Rosa"
                autofocus class="border-none focus:ring-0 p-2 bg-transparent w-full" />
        </div>

        @if ($usuarios->isEmpty())
            <p class="text-2xl text-center font-bold">Aún no has agregado empleados</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Imagen</th>
                            <th class="px-4 py-2">Usuario</th>
                            <th class="px-4 py-2">Rol</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr class="usuarioId text-center hover:bg-indigo-100 hover:cursor-pointer"
                                wire:key='{{ $usuario->id }}'>
                                <td class="border px-4 capitalize">{{ $usuario->name }}</td>
                                <td class="border px-4">
                                    <img loading="lazy" src="{{ asset('storage/uploads/imagenes_usuarios/' . $usuario->imagen) }}"
                                        alt="Imagen del usuario"
                                        class="object-cover my-2 w-16 h-16 rounded-full mx-auto" />
                                </td>
                                <td class="border px-4 capitalize">{{ $usuario->usuario }}</td>

                                <td class="border px-4 capitalize">{{ $usuario->rol == 1 ? 'Admin' : 'Cajero' }}</td>
                                <td class="border px-4">
                                    <button wire:click.prevent="cambiarEstado({{ $usuario->id }})"
                                        class="focus:outline-none text-2xl"
                                        title="{{ $usuario->estado == 1 ? 'Desactivar' : 'Activar' }}">
                                        <i
                                            class="fa-solid {{ $usuario->estado == 1 ? 'fa-check text-green-500' : 'fa-xmark text-red-500' }}"></i>
                                    </button>
                                </td>
                                <td class="border px-4">
                                    <div class="flex gap-1 w-full justify-center">
                                        <button class="bg-indigo-600 text-lg px-2 py-1 text-white hover:cursor-pointer"
                                            wire:click.prevent="abrirModalEditar({{ $usuario->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        @if ($usuario->id !== $usuarioAutenticado->id)
                                            <button
                                                wire:click.prevent="$dispatch('confirmarEliminarUsuario', { usuarioId: {{ $usuario->id }} })"
                                                class="bg-red-500 text-lg px-2 py-1 text-white hover:cursor-pointer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($modalAgregar || $modalEditar)
        <div wire:transition.opacity.duration.100ms
            class="transition-all ease-in fixed bg-[rgba(0,0,0,0.5)] backdrop-blur top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white p-4 rounded-lg w-full max-w-2xl">
                    <form wire:submit.prevent="{{ $modalAgregar ? 'crearUsuario' : 'actualizarUsuario' }}"
                        enctype="multipart/form-data">
                        <legend class="text-2xl font-bold text-gray-600 text-center mb-2">
                            {{ $modalAgregar ? 'Agregar Empleado' : 'Actualizar Empleado' }}</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                @if ($modalAgregar)
                                    @if ($imagen)
                                        <img loading="lazy" src="{{ $imagen->temporaryUrl() }}" alt="Imagen previa"
                                            class="object-cover mb-2 w-24" />
                                    @endif
                                    <x-input-label class="text-gray-500" for="imagen" :value="__('Imagen')" />
                                    <x-text-input wire:model="imagen" id="imagen"
                                        class="border-none text-xs p-0 block mt-1 w-full capitalize text-white"
                                        type="file" name="imagen" accept="image/*" />
                                    <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                                @else
                                    @if ($imagen)
                                        <img loading="lazy" src="{{ asset('storage/uploads/imagenes_usuarios/' . $imagen) }}"
                                            alt="Imagen previa" class="object-cover mb-2 w-24" />
                                    @endif
                                    <x-input-label class="text-gray-500" for="imagen" :value="__('Imagen')" />
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
                                <x-input-label class="hidden" for="name" :value="__('Nombre')" />
                                <x-text-input placeholder="Nombre Completo" wire:model="name" id="name" class="block mt-1 w-full capitalize"
                                    type="text" name="name" autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="email" :value="__('Correo')" />
                                <x-text-input placeholder="Correo electrónico" wire:model="email" id="email" class="block mt-1 w-full"
                                    type="email" name="email" autocomplete="email" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="rol" :value="__('Rol')" />
                                <select wire:model="rol" id="rol" name="rol"
                                    class="p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                                    <option selected>--- Rol ---</option>
                                    <option value="0">Cajero</option>
                                    <option value="1">Admin</option>
                                </select>
                                <x-input-error :messages="$errors->get('rol')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="usuario" :value="__('Nombre Usuario')" />
                                <x-text-input placeholder="Nombre de usuario" wire:model="usuario" id="usuario"
                                    class="block mt-1 w-full capitalize" type="text" name="usuario"
                                    autocomplete="usuario" />
                                <x-input-error :messages="$errors->get('usuario')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="estado" :value="__('Estado')" />
                                <select wire:model="estado" id="estado" name="estado"
                                    class="p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                                    <option selected>--- Estado ---</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="password" :value="__('Contraseña')" />
                                <x-text-input placeholder="Contraseña" wire:model="password" id="password" class="block mt-1 w-full"
                                    type="password" name="password" autocomplete="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="mb-2">
                                <x-input-label class="hidden" for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input placeholder="Confirmar contraseña" wire:model="password_confirmation" id="password_confirmation"
                                    class="block mt-1 w-full" type="password" name="password_confirmation"
                                    autocomplete="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-span-2 mt-4">
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
                </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("livewire:init", function() {
            Livewire.on("confirmarEliminarUsuario", usuarioId => {
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
                        Livewire.dispatch("eliminarUsuario", usuarioId);
                    }
                });
            });

            Livewire.on("usuarioEliminado", () => {
                Swal.fire({
                    title: "Usuario Eliminado",
                    text: "El usuario ha sido eliminado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("usuarioCreado", () => {
                Swal.fire({
                    title: "Usuario Creado",
                    text: "El usuario se ha creado correctamente.",
                    icon: "success"
                });
            });

            Livewire.on("usuarioActualizado", () => {
                Swal.fire({
                    title: "Usuario Actualizado",
                    text: "El usuario se ha actualizado correctamente.",
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

            let usuarios = document.querySelectorAll(".usuarioId");

            usuarios.forEach(usuario => {
                usuario.addEventListener("dblclick", e => {
                    e.preventDefault();
                    let usuarioId = e.currentTarget.getAttribute('wire:key');
                    Livewire.dispatch("abrirModalEditar", {
                        id: usuarioId
                    });
                });
            });
        });
    </script>
@endpush
