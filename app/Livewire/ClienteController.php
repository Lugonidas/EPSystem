<?php

namespace App\Livewire;

use App\Exports\ClientesExport;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Component
{
    use WithPagination, WithFileUploads;

    public $clientes;
    public $cliente_id;
    public $nombre;
    public $numero_identificacion;
    public $celular;
    public $email;
    public $ciudad;
    public $barrio;
    public $direccion;
    public $imagen;
    public $imagen_nueva;

    public $busqueda = "";

    public $modalAgregar = false;
    public $modalEditar = false;

    public $listeners = ["confirmarEliminarCliente"];

    public function render()
    {
        $this->buscarClientes();

        return view('livewire.cliente-controller')->layout("layouts.app");
    }

    public function buscarClientes()
    {
        // Solo realiza la búsqueda si se ha introducido algo en el campo de búsqueda
        if ($this->busqueda) {
            $this->clientes = Cliente::where('numero_identificacion', 'like', '%' . $this->busqueda . '%')
                ->get();
        } else {
            // Si no hay búsqueda, obtén todos los clientes
            $this->clientes = Cliente::all();
        }
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

        // Cargar los datos del cliente para editar
        $cliente = Cliente::find($id);

        // Llenar los campos del formulario con los detalles del cliente
        $this->cliente_id = $cliente->id;
        $this->nombre = $cliente->nombre;
        $this->numero_identificacion = $cliente->numero_identificacion;
        $this->celular = $cliente->celular;
        $this->email = $cliente->email;
        $this->ciudad = $cliente->ciudad;
        $this->barrio = $cliente->barrio;
        $this->direccion = $cliente->direccion;
        $this->imagen = $cliente->imagen;
        $this->modalEditar = true;
    }

    public function cerrarModalEditar()
    {
        $this->modalEditar = false;
    }

    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalAgregar();
        } else {
            return;
        }
    }

    public function crearCliente()
    {
        $this->validatecliente();

        $nombreImagen = null;

        // Verificar si se ha cargado una imagen
        if ($this->imagen) {
            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen->extension();

            // Almacenar la imagen en la carpeta "uploads" con un nombre único
            $this->imagen->storeAs('uploads/imagenes_clientes', $nombreImagen, 'public');
        } else {
            $nombreImagen = 'user-default.png';
        }

        Cliente::create([
            'nombre' => $this->nombre,
            'numero_identificacion' => $this->numero_identificacion,
            'celular' => $this->celular,
            'email' => $this->email,
            'ciudad' => $this->ciudad,
            'barrio' => $this->barrio,
            'direccion' => $this->direccion,
            'imagen' => $nombreImagen,
        ]);

        $this->dispatch('clienteCreado');
        $this->cerrarModalAgregar();
        $this->resetForm();
    }

    public function actualizarCliente()
    {
        $this->validatecliente();

        $clienteActualizado = Cliente::find($this->cliente_id);

        $nombreImagen = $clienteActualizado->imagen;

        // Si se carga una nueva imagen, almacenarla y actualizar el nombre de la imagen en la base de datos
        if ($this->imagen_nueva) {
            // Eliminar la imagen anterior si existe
            if ($clienteActualizado->imagen && $clienteActualizado->imagen !== 'user-default.png' && Storage::disk('public')->exists('uploads/imagenes_clientes/' . $clienteActualizado->imagen)) {
                Storage::disk('public')->delete('uploads/imagenes_clientes/' . $clienteActualizado->imagen);
            }

            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen_nueva->extension();

            // Almacenar la imagen en la carpeta "uploads" con un nombre único
            $this->imagen_nueva->storeAs('uploads/imagenes_clientes', $nombreImagen, 'public');
            $clienteActualizado->imagen = $nombreImagen;
        }

        // Actualizar el resto de los campos
        $clienteActualizado->update([
            'nombre' => $this->nombre,
            'numero_identificacion' => $this->numero_identificacion,
            'celular' => $this->celular,
            'email' => $this->email,
            'ciudad' => $this->ciudad,
            'barrio' => $this->barrio,
            'direccion' => $this->direccion,
        ]);

        $this->dispatch('clienteActualizado');
        $this->cerrarModalEditar();
        $this->resetForm();
    }

    #[On('eliminarCliente')]
    public function eliminarCliente($clienteId)
    {
        // Encuentra el cliente por su ID y elimínalo
        Cliente::find($clienteId)->delete();

        $this->dispatch("clienteEliminado");
    }

    public function generarPDFClientes()
    {
        $clientes = Cliente::all();

        if (!$clientes) {
            $this->dispatch("noHayClientes");
        }

        $pdf = Pdf::loadView('livewire.recibo-clientes', [
            "clientes" => $clientes
        ]);


        return $pdf->stream('ReciboUsuarios.pdf');
    }

    public function generarExcelClientes()
    {
        return Excel::download(new ClientesExport, 'clientes.xlsx');
    }

    private function validateCliente()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'numero_identificacion' => ['required', 'numeric', Rule::unique('Clientes', 'numero_identificacion')->ignore($this->cliente_id)],
            'celular' => ['numeric', 'regex:/^[0-9]{10}$/'],
            'email' => ['nullable', 'email', 'max:60', Rule::unique('clientes', 'email')->ignore($this->cliente_id)],
            'ciudad' => ['max:255', 'string'],
            'barrio' => ['max:255', 'string'],
            'direccion' => ['max:255', 'string']
        ];

        return $this->validate($rules, [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',

            'numero_identificacion.required' => 'El campo numero de identificación es obligatorio.',
            'numero_identificacion.numeric' => 'El campo número de identificación debe ser numerico.',

            'celular.numeric' => 'El campo celular debe ser numerico.',
            'celular.regex' => 'El campo celular debe contener 10 digitos.',

            'email.email' => 'El campo correo debe ser una dirección de correo electrónico válida.',
            'email.unique' => 'El correo ya está en uso.',

            'ciudad.string' => 'El campo ciudad debe ser una cadena de caracteres.',
            'ciudad.max' => 'El campo ciudad no puede exceder los 255 caracteres.',

            'barrio.string' => 'El campo barrio debe ser una cadena de caracteres.',
            'barrio.max' => 'El campo barrio no puede exceder los 255 caracteres.',

            'direccion.string' => 'El campo direccion debe ser una cadena de caracteres.',
            'direccion.max' => 'El campo direccion no puede exceder los 255 caracteres.',

        ]);
    }

    private function resetForm()
    {
        $this->cliente_id = null;
        $this->nombre = '';
        $this->numero_identificacion = '';
        $this->celular = '';
        $this->email = '';
        $this->ciudad = '';
        $this->barrio = '';
        $this->direccion = '';
        $this->imagen = '';
        $this->imagen_nueva = '';

        $this->resetValidation();
    }
}
