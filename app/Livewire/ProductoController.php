<?php

namespace App\Livewire;

use App\Exports\ProductosExport;
use App\Http\Controllers\ScaleController;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Illuminate\Database\QueryException;
use lepiaf\SerialPort\SerialPort;
use lepiaf\SerialPort\Parser\SeparatorParser;
use lepiaf\SerialPort\Configure\TTYConfigure;

class ProductoController extends Component
{
    use WithPagination, WithFileUploads;

    public $productos;
    public $categorias;
    public $proveedores;
    public $producto_id;
    public $categoria_id;
    public $proveedor_id;
    public $unidad_medida;
    public $imagen_nueva;
    public $nombre;
    public $codigo;
    public $precio;
    public $stock;
    public $impuesto;
    public $imagen;
    public $estado;
    public $peso;

    public $busqueda = "";
    public $filtro = null;
    public $paginado = 5;

    public $modalAgregar = false;
    public $modalEditar = false;

    public function render()
    {
        $this->proveedores = Proveedor::all();
        $this->categorias = Categoria::all();
        // Solo realiza la búsqueda si se ha introducido algo en el campo de búsqueda
        if ($this->busqueda) {
            $this->productos = Producto::where('nombre', 'like', '%' . $this->busqueda . '%')->orWhere('codigo', 'like', '%' . $this->busqueda . '%')->get();
        } else if ($this->filtro) {
            $this->productos = Producto::where('categoria_id', $this->filtro)->get();
        } else {
            // Si no hay búsqueda, obtén todos los productos
            $this->productos = Producto::all();
        }

        return view('livewire.producto-controller')->layout("layouts.app");
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
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('abrirModalEditar')]
    public function abrirModalEditar($id)
    {
        $this->resetForm();

        // Cargar los datos del producto para editar
        $producto = Producto::find($id);

        
        // Verifica que el producto existe antes de intentar llenar los campos
        if ($producto) {
            // Llenar los campos del formulario con los detalles del producto
            $this->producto_id = $producto->id;
            $this->nombre = $producto->nombre;
            $this->codigo = $producto->codigo;
            $this->precio = number_format($producto->precio, 2, '.', '');
            $this->stock = $producto->stock;
            $this->categoria_id = $producto->categoria_id;
            $this->proveedor_id = $producto->proveedor_id;
            $this->impuesto = $producto->impuesto;
            $this->unidad_medida = $producto->unidad_medida;
            $this->imagen = $producto->imagen;
            $this->estado = $producto->estado;

            $this->modalEditar = true;
        } else {
            // Manejo de errores si no se encuentra el producto
        }
    }

    public function cerrarModalEditar()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->modalEditar = false;
    }

    public function generarPDFProductos()
    {
        $productos = Producto::all();

        if (!$productos) {
            $this->dispatch("noHayproductos");
        }

        $pdf = PDF::loadView('pdf.recibo-productos', [
            "productos" => $productos
        ]);


        return $pdf->stream('ReciboProductos.pdf');
    }

    public function generarExcelProductos()
    {
        return Excel::download(new ProductosExport, 'productos.xlsx');
    }

    public function crearProducto()
    {
        $this->validateProducto();

        $nombreImagen = null;

        // Verificar si se ha cargado una imagen
        if ($this->imagen) {
            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen->extension();

            // Almacenar la imagen en la carpeta "uploads" con un nombre único
            $this->imagen->storeAs('uploads/imagenes_productos', $nombreImagen, 'public');
        } else {
            $nombreImagen = 'user-default.png';
        }

        Producto::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria_id' => $this->categoria_id,
            'proveedor_id' => $this->proveedor_id,
            'impuesto' => $this->impuesto ? $this->impuesto : 0,
            'unidad_medida' => $this->unidad_medida,
            'imagen' => $nombreImagen,
            'estado' => $this->estado,
        ]);

        $this->cerrarModalAgregar();
        $this->dispatch('productoCreado');

        return redirect()->route("productos");
    }

    public function actualizarProducto()
    {

        $productoActulizado = Producto::find($this->producto_id);

        $nombreImagen = $productoActulizado->imagen;

        if ($this->imagen_nueva) {
            // Eliminar la imagen anterior si existe
            if ($productoActulizado->imagen && $productoActulizado->imagen !== 'user-default.png' && Storage::disk('public')->exists('uploads/imagenes_productos/' . $productoActulizado->imagen)) {
                Storage::disk('public')->delete('uploads/imagenes_productos/' . $productoActulizado->imagen);
            }

            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid()) . '.' . $this->imagen_nueva->extension();

            // Almacenar la imagen en la carpeta "uploads" con un nombre único
            $this->imagen_nueva->storeAs('uploads/imagenes_productos', $nombreImagen, 'public');
            $productoActulizado->imagen = $nombreImagen;
        }

        $this->precio = str_replace('.', '', $this->precio);

        // Actualizar el resto de los campos
        $productoActulizado->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria_id' => $this->categoria_id,
            'proveedor_id' => $this->proveedor_id,
            'impuesto' => $this->impuesto,
            'unidad_medida' => $this->unidad_medida,
            'estado' => $this->estado,
        ]);

        $this->dispatch('productoActualizado');
        $this->cerrarModalEditar();
    }

    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalAgregar();
        } else {
            return;
        }
    }

    public function formatearDinero($numero)
    {
        $numero = number_format($numero, 0, ',', '.');
        return $numero;
    }

    public function updatedPrecio($value)
    {
        // Eliminar cualquier carácter no numérico, excepto comas y puntos
        $numericValue = preg_replace('/[^\d]/', '', $value);

        // Formatear el número con puntos como separadores de miles
        $this->precio = number_format($numericValue, 0, '', '.');
    }

    #[On('eliminarProducto')]
    public function eliminarProducto($productoId)
    {
        // Encuentra el producto por su ID y elimínalo
        $producto = Producto::find($productoId);

        if ($producto) {
            try {
                $producto->delete();
                $this->dispatch("productoEliminado");
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1451) {
                    $this->dispatch("productoErrorEliminar");
                    session()->flash('error', 'No se puede eliminar el producto porque está siendo utilizado en una venta.');
                } else {
                    $this->dispatch("productoErrorEliminar");
                    session()->flash('error', 'Ocurrió un error al intentar eliminar el producto: ' . $e->getMessage());
                }
            }
        } else {
            // Manejar el caso donde la categoría no existe
            session()->flash('error', 'Producto no encontrado.');
        }
    }


    public function cambiarEstado($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->estado = $producto->estado === 1 ? 0 : 1;
            $producto->save();

            // Opcional: Emitir un evento o hacer algo después de cambiar el estado
            $this->dispatch('estadoCambiado');
        } else {
            session()->flash('error', 'Producto no encontrado.');
        }
    }

    private function validateProducto()
    {
        return $this->validate([
            'nombre' => ['required', 'string', 'max:40'],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique('productos', 'codigo')->ignore($this->producto_id),
            ],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'numeric', 'exists:categorias,id'],
            'proveedor_id' => ['required', 'numeric', 'exists:proveedores,id'],
            'impuesto' => ['nullable', 'numeric', 'min:0'],
            'unidad_medida' => ['required', 'string', 'max:50'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'estado' => ['required', 'boolean']
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',

            'codigo.required' => 'El campo código es obligatorio.',
            'codigo.string' => 'El campo código debe ser una cadena de caracteres.',
            'codigo.unique' => 'El código ya está en uso.',
            'codigo.max' => 'El campo código no puede exceder los 50 caracteres.',

            'precio.required' => 'El campo precio es obligatorio.',
            'precio.numeric' => 'El campo precio debe ser un valor numérico.',
            'precio.min' => 'El campo precio debe ser mayor o igual a 0.',

            'stock.required' => 'El campo stock es obligatorio.',
            'stock.integer' => 'El campo stock debe ser un valor entero.',
            'stock.min' => 'El campo stock debe ser mayor o igual a 0.',

            'categoria_id.required' => 'El campo categoría es obligatorio.',
            'categoria_id.numeric' => 'El campo categoría debe ser un número.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',

            'proveedor_id.required' => 'El campo proveedor es obligatorio.',
            'proveedor_id.numeric' => 'El campo proveedor debe ser un número.',
            'proveedor_id.exists' => 'La proveedor seleccionado no existe.',

            'impuesto.numeric' => 'El campo impuesto debe ser un valor numérico.',
            'impuesto.min' => 'El campo impuesto debe ser mayor o igual a 0.',

            'imagen.max' => 'La imagen no puede ser mayor de 2MB.',

            'unidad_medida.string' => 'El campo unidad de medida debe ser una cadena de caracteres.',
            'unidad_medida.max' => 'El campo unidad de medida no puede exceder los 50 caracteres.',
            'unidad_medida.required' => 'El campo unidad de medida es obligatorio.',

            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado no es válido.'
        ]);
    }

    private function resetForm()
    {
        $this->producto_id = null;
        $this->nombre = '';
        $this->codigo = '';
        $this->precio = '';
        $this->stock = '';
        $this->categoria_id = '';
        $this->proveedor_id = '';
        $this->impuesto = '';
        $this->unidad_medida = '';
        $this->imagen = '';
        $this->estado = null;
    }
}
