<?php

namespace App\Livewire;

use App\Exports\ProveedoresExport;
use App\Models\Proveedor;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Component
{
    use WithPagination, WithFileUploads;

    public $proveedores;

    public $proveedor_id;
    public $nombre;
    public $email;
    public $celular;
    public $estado;

    public $busqueda = "";

    public $modalAgregar = false;
    public $modalEditar = false;

    public $usuarioAutenticado;

    protected $listeners = ["confirmarEliminarCategoria"];

    public function mount()
    {
        $this->usuarioAutenticado = auth()->user();
    }

    public function render()
    {
        // Solo realiza la búsqueda si se ha introducido algo en el campo de búsqueda
        if ($this->busqueda) {
            $this->proveedores = Proveedor::where('nombre', 'like', '%' . $this->busqueda . '%')
                ->get();
        } else {
            // Si no hay búsqueda, obtén todos los categorias
            $this->proveedores = Proveedor::all();
        }

        return view('livewire.proveedor-controller')->layout("layouts.app");
    }

    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalAgregar();
        } else {
            return;
        }
    }

    public function generarPDFProveedores()
    {
        $proveedores = Proveedor::all();

        if (!$proveedores) {
            $this->dispatch("noHayProveedores");
        }

        $pdf = PDF::loadView('livewire.recibo-proveedores', [
            "proveedores" => $proveedores
        ]);

        return $pdf->stream('ReciboProveedores.pdf');
    }

    public function generarExcelProveedores()
    {
        return Excel::download(new ProveedoresExport, 'proveedores.xlsx');
    }

    // Métodos para abrir y cerrar modales
    public function abrirModalAgregar()
    {
        $this->resetForm();
        $this->modalAgregar = true;
    }

    public function cerrarModalAgregar()
    {
        $this->modalAgregar = false;
    }

    #[On('abrirModalEditar')]
    public function abrirModalEditar($id)
    {
        $this->resetForm();

        // Cargar los datos del usuario para editar
        $proveedor = Proveedor::find($id);

        // Llenar los campos del formulario con los detalles del usuario
        $this->proveedor_id = $proveedor->id;
        $this->nombre = $proveedor->nombre;
        $this->email = $proveedor->email;
        $this->celular = $proveedor->celular;
        $this->estado = $proveedor->estado;
        $this->modalEditar = true;
    }

    public function cerrarModalEditar()
    {
        $this->modalEditar = false;
        $this->resetValidation();
    }

    public function crearProveedor()
    {
        $this->validateProveedor();

        Proveedor::create([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'celular' => $this->celular,
            'estado' => $this->estado,
        ]);

        $this->dispatch('proveedorCreado');
        $this->cerrarModalAgregar();
        $this->resetForm();
    }

    public function actualizarProveedor()
    {
        $this->validateProveedor();

        $proveedorActualizado = Proveedor::find($this->proveedor_id);

        // Actualizar el resto de los campos
        $proveedorActualizado->update([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'celular' => $this->celular,
            'estado' => $this->estado,
        ]);

        $this->dispatch('proveedorActualizado');
        $this->cerrarModalEditar();
        $this->resetForm();
    }


    #[On('eliminarProveedor')]
    public function eliminarProveedor($proveedor_id)
    {
        // Encuentra la categoría por su ID
        $proveedor = Proveedor::find($proveedor_id);

        if ($proveedor) {
            // Verificar si la categoría tiene productos relacionados
            $productosRelacionados = $proveedor->productos()->exists();

            if ($productosRelacionados) {
                $this->dispatch('proveedorError');
                session()->flash('error', 'No se puede eliminar el proveedor porque tiene productos relacionados.');
            } else {
                // Eliminar la proveedor si no tiene productos relacionados
                $proveedor->delete();
                $this->dispatch('proveedorEliminado');
                session()->flash('success', 'Proveedor eliminado exitosamente.');
            }
        } else {
            // Manejar el caso donde la proveedor no existe
            session()->flash('error', 'Proveedor no encontrado.');
        }
    }

    public function cambiarEstado($id)
    {
        $proveedor = Proveedor::find($id);

        if ($proveedor) {
            $proveedor->estado = $proveedor->estado === 1 ? 0 : 1;
            $proveedor->save();

            // Opcional: Emitir un evento o hacer algo después de cambiar el estado
            $this->dispatch('estadoCambiado');
        } else {
            session()->flash('error', 'Proveedor no encontrado.');
        }
    }


    private function validateProveedor()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:80',
                Rule::unique('proveedores', 'email')->ignore($this->proveedor_id)
            ],
            'celular' => ['required', 'string', 'max:10'],
            'estado' => ['nullable', 'boolean'],
        ];

        $messages = [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',

            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo electrónico válida.',
            'email.max' => 'El campo email no puede exceder los 80 caracteres.',
            'email.unique' => 'El campo email ya está en uso.',

            'celular.required' => 'El campo celular es obligatorio.',
            'celular.string' => 'El campo teléfono debe ser una cadena de caracteres.',
            'celular.max' => 'El campo teléfono no puede exceder los 20 caracteres.',

            'estado.boolean' => 'El campo estado debe ser un valor booleano.',
        ];

        return $this->validate($rules, $messages);
    }

    private function resetForm()
    {
        $this->proveedor_id = null;
        $this->nombre = '';
        $this->email = '';
        $this->celular = '';
        $this->estado = 1;
    }
}
