<?php

namespace App\Livewire;

use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\pdf as PDF;

/**
 * Clase UsuarioController
 *
 * Controlador Livewire para gestionar la funcionalidad relacionada con los usuarios.
 * Incluye características como la creación, actualización, eliminación y exportación de usuarios.
 */
class UsuarioController extends Component
{
    use WithPagination, WithFileUploads;

    // Propiedades relacionadas con la información de los usuarios
    public $usuarios;
    public $usuario_id;
    public $name;
    public $usuario;
    public $email;
    public $numero_identificacion;
    public $rol = 0;
    public $estado = 1;
    public $imagen;
    public $imagen_nueva;
    public $password;
    public $password_confirmation;

    // Propiedad para manejar el término de búsqueda de usuarios
    public $busqueda = "";

    // Propiedades para controlar la visibilidad de los modales
    public $modalAgregar = false;
    public $modalEditar = false;

    // Usuario autenticado actualmente
    public $usuarioAutenticado;

    // Listeners para eventos emitidos por otros componentes
    protected $listeners = ["confirmarEliminarUsuario"];

    /**
     * Método mount
     *
     * Inicializa el componente y asigna el usuario autenticado.
     */
    public function mount()
    {
        $this->usuarioAutenticado = auth()->user();
    }

    /**
     * Método render
     *
     * Renderiza la vista del componente. Filtra los usuarios según la búsqueda o muestra todos los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Realiza la búsqueda si hay un término de búsqueda
        if ($this->busqueda) {
            $this->usuarios = User::where('name', 'like', '%' . $this->busqueda . '%')
                ->get();
        } else {
            // Obtiene todos los usuarios si no hay búsqueda
            $this->usuarios = User::all();
        }
        return view('livewire.usuario-controller')->layout("layouts.app");
    }

    /**
     * Método teclaPresionada
     *
     * Abre el modal de agregar usuario si se presiona la tecla F2.
     *
     * @param string $tecla
     */
    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalAgregar();
        } else {
            return;
        }
    }

    /**
     * Método generarPDFUsuarios
     *
     * Genera un PDF con la lista de usuarios.
     *
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generarPDFUsuarios()
    {
        $usuarios = User::all();

        if (!$usuarios) {
            $this->dispatch("noHayUsuarios");
        }

        $pdf = PDF::loadView('livewire.recibo-usuarios', [
            "usuarios" => $usuarios
        ]);

        return $pdf->stream('ReciboUsuarios.pdf');
    }

    /**
     * Método generarExcelUsuarios
     *
     * Genera un archivo Excel con la lista de usuarios.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generarExcelUsuarios()
    {
        return Excel::download(new UsuariosExport, 'usuarios.xlsx');
    }

    /**
     * Método abrirModalAgregar
     *
     * Abre el modal para agregar un nuevo usuario y resetea el formulario.
     */
    public function abrirModalAgregar()
    {
        $this->resetForm();
        $this->modalAgregar = true;
    }

    /**
     * Método abrirModalEditar
     *
     * Abre el modal para editar un usuario existente.
     * 
     * @On("abrirModalEditar")
     * @param int $id ID del usuario a editar.
     */
    #[On("abrirModalEditar")]
    public function abrirModalEditar($id)
    {
        $this->resetForm();

        // Cargar los datos del usuario para editar
        $usuario = User::find($id);

        // Llenar los campos del formulario con los detalles del usuario
        $this->usuario_id = $usuario->id;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->rol = $usuario->rol;
        $this->numero_identificacion = $usuario->numero_identificacion;
        $this->usuario = $usuario->usuario;
        $this->estado = $usuario->estado;
        $this->imagen = $usuario->imagen;
        $this->modalEditar = true;
    }

    /**
     * Método cerrarModalAgregar
     *
     * Cierra el modal de agregar usuario.
     */
    public function cerrarModalAgregar()
    {
        $this->modalAgregar = false;
    }

    /**
     * Método cerrarModalEditar
     *
     * Cierra el modal de editar usuario.
     */
    public function cerrarModalEditar()
    {
        $this->modalEditar = false;
    }

    /**
     * Método crearUsuario
     *
     * Crea un nuevo usuario después de validar los datos del formulario.
     */
    public function crearUsuario()
    {
        $this->validateUsuario();

        $nombreImagen = null;

        // Verificar si se ha cargado una imagen
        if ($this->imagen) {
            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen->extension();

            // Almacenar la imagen en la carpeta "uploads" con un nombre único
            $this->imagen->storeAs('uploads/imagenes_usuarios', $nombreImagen, 'public');
        } else {
            $nombreImagen = 'user-default.png';
        }

        // Crear un nuevo usuario con los datos del formulario
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'numero_identificacion' => $this->numero_identificacion,
            'rol' => $this->rol,
            'usuario' => $this->usuario,
            'estado' => $this->estado,
            'imagen' => $nombreImagen,
        ]);

        $this->dispatch('usuarioCreado');
        $this->cerrarModalAgregar();
        $this->resetForm();
    }

    /**
     * Método actualizarUsuario
     *
     * Actualiza la información de un usuario existente.
     */
    public function actualizarUsuario()
    {
        $this->validateUsuario();

        $usuarioActualizado = User::find($this->usuario_id);

        $nombreImagen = $usuarioActualizado->imagen;

        // Verificar si se ha cargado una nueva imagen
        if ($this->imagen_nueva) {
            // Eliminar la imagen anterior si existe
            if ($usuarioActualizado->imagen && $usuarioActualizado->imagen !== 'user-default.png' && Storage::disk('public')->exists('uploads/imagenes_usuarios/' . $usuarioActualizado->imagen)) {
                Storage::disk('public')->delete('uploads/imagenes_usuarios/' . $usuarioActualizado->imagen);
            }

            // Generar un nombre único para la nueva imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen_nueva->extension();

            // Almacenar la nueva imagen en la carpeta "uploads"
            $this->imagen_nueva->storeAs('uploads/imagenes_usuarios', $nombreImagen, 'public');
            $usuarioActualizado->imagen = $nombreImagen;
        }

        // Actualizar los datos del usuario con los valores del formulario
        $usuarioActualizado->update([
            'name' => $this->name,
            'email' => $this->email,
            'rol' => $this->rol,
            'numero_identificacion' => $this->numero_identificacion,
            'usuario' => $this->usuario,
            'estado' => $this->estado,
        ]);

        $this->dispatch('usuarioActualizado');
        $this->cerrarModalEditar();
        $this->resetForm();
    }

    /**
     * Método eliminarUsuario
     *
     * Elimina un usuario específico del sistema.
     * 
     * @On("eliminarUsuario")
     * @param int $id_usuario ID del usuario a eliminar.
     */
    public function eliminarUsuario($id_usuario)
    {
        // Verifica si el usuario autenticado está intentando eliminarse a sí mismo
        if ($id_usuario == $this->usuarioAutenticado->id) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
            return;
        }

        // Encuentra y elimina el usuario por su ID
        $usuario = User::find($id_usuario);

        if ($usuario) {
            $usuario->delete();
            $this->dispatch('usuarioEliminado');
            session()->flash('success', 'Usuario eliminado exitosamente');
        } else {
            // Manejar el caso donde el usuario no existe
            session()->flash('error', 'Usuario no encontrado.');
        }
    }

    public function cambiarEstado($id)
    {
        $usuario = User::find($id);

        if ($usuario) {
            $usuario->estado = $usuario->estado === 1 ? 0 : 1;
            $usuario->save();

            // Opcional: Emitir un evento o hacer algo después de cambiar el estado
            $this->dispatch('estadoCambiado');
        } else {
            session()->flash('error', 'Usuario no encontrado.');
        }
    }


    /**
     * Método validateUsuario
     *
     * Valida los campos del formulario de usuario.
     */
    private function validateUsuario()
    {
        // Validar los datos del formulario de usuario
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->usuario_id)],
            'numero_identificacion' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($this->usuario_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'usuario' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->usuario_id)],
            'rol' => ['required', 'integer', 'between:0,2'],
            'estado' => ['required', 'integer', 'between:0,1'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'El correo ya está registrado.',
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'numero_identificacion.unique' => 'El número de identificación ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'usuario.unique' => 'El nombre de usuario ya está registrado.',
            'rol.required' => 'El rol es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
        ]);
    }

    /**
     * Método resetForm
     *
     * Restaura los campos del formulario a sus valores predeterminados.
     */
    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->numero_identificacion = '';
        $this->usuario = '';
        $this->rol = 0;
        $this->estado = 1;
        $this->imagen = null;
        $this->imagen_nueva = null;
    }
}
