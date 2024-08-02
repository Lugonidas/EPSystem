<?php

namespace App\Livewire;

use App\Exports\CategoriasExport;
use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;

class CategoriaController extends Component
{
    use WithPagination, WithFileUploads;

    public $categorias;

    public $categoria_id;
    public $nombre;
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
            $this->categorias = Categoria::where('nombre', 'like', '%' . $this->busqueda . '%')
                ->get();
        } else {
            // Si no hay búsqueda, obtén todos los categorias
            $this->categorias = Categoria::all();
        }

        return view('livewire.categoria-controller')->layout("layouts.app");
    }

    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalAgregar();
        } else {
            return;
        }
    }

    public function generarPDFCategorias()
    {
        $categorias = Categoria::all();

        if (!$categorias) {
            $this->dispatch("noHayCategorias");
        }

        $pdf = PDF::loadView('livewire.recibo-categorias', [
            "categorias" => $categorias
        ]);

        return $pdf->stream('ReciboCategorias.pdf');
    }

    public function generarExcelCategorias()
    {
        return Excel::download(new CategoriasExport, 'categorias.xlsx');
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
        $categoria = Categoria::find($id);

        // Llenar los campos del formulario con los detalles del usuario
        $this->categoria_id = $categoria->id;
        $this->nombre = $categoria->nombre;
        $this->estado = $categoria->estado;
        $this->modalEditar = true;
    }

    public function cerrarModalEditar()
    {
        $this->modalEditar = false;
    }

    public function crearCategoria()
    {
        $this->validateCategoria();

        Categoria::create([
            'nombre' => $this->nombre,
            'estado' => $this->estado,
        ]);

        $this->dispatch('categoriaCreada');
        $this->cerrarModalAgregar();
        $this->resetForm();
    }

    public function actualizarCategoria()
    {
        $this->validateCategoria();

        $categoriaActualizada = Categoria::find($this->categoria_id);

        // Actualizar el resto de los campos
        $categoriaActualizada->update([
            'nombre' => $this->nombre,
            'estado' => $this->estado,
        ]);

        $this->dispatch('categoriaActualizada');
        $this->cerrarModalEditar();
        $this->resetForm();
    }


    #[On('eliminarCategoria')]
    public function eliminarCategoria($categoria_id)
    {
        // Encuentra la categoría por su ID
        $categoria = Categoria::find($categoria_id);

        if ($categoria) {
            // Verificar si la categoría tiene productos relacionados
            $productosRelacionados = $categoria->productos()->exists();

            if ($productosRelacionados) {
                $this->dispatch('categoriaError');
                session()->flash('error', 'No se puede eliminar la categoría porque tiene productos relacionados.');
            } else {
                // Eliminar la categoría si no tiene productos relacionados
                $categoria->delete();
                $this->dispatch('categoriaEliminada');
                session()->flash('success', 'Categoría eliminada exitosamente.');
            }
        } else {
            // Manejar el caso donde la categoría no existe
            session()->flash('error', 'Categoría no encontrada.');
        }
    }

    public function cambiarEstado($id)
    {
        $categoria = Categoria::find($id);

        if ($categoria) {
            $categoria->estado = $categoria->estado === 1 ? 0 : 1;
            $categoria->save();

            // Opcional: Emitir un evento o hacer algo después de cambiar el estado
            $this->dispatch('estadoCambiado');
        } else {
            session()->flash('error', 'Categoria no encontrada.');
        }
    }



    private function validateCategoria()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ];

        return $this->validate($rules, [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',

            'estado.boolean' => 'El campo estado debe ser un valor booleano.',
        ]);
    }

    private function resetForm()
    {
        $this->categoria_id = null;
        $this->nombre = '';
        $this->estado = null;
    }
}
