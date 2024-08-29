<?php

namespace App\Livewire;

use App\Helpers\Helpers;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Livewire\Attributes\On;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\VarDumper\VarDumper;

class VentaController extends Component
{
    public $productos = [];
    public $ultimaVenta;
    public $usuario_id;
    public $total;
    public $subTotal;
    public $observaciones;
    public $numero_factura;
    public $carrito = [];
    public $carritoRecibo = [];
    public $cantidad = [];
    public $busqueda;
    public $busquedaCliente;
    public $busquedaVenta;
    public $clientes = [];
    public $cliente;
    public $recibido;
    public $cambio;
    public $numero_recibo;
    public $venta;
    public $ventas = [];

    public $modalProductos = false;

    public $modalClientes = false;

    public $modalVentas = false;

    public $modalRecibo = false;

    protected $listeners = ["cerrarModalRecibo"];

    public function mount()
    {
        $this->recibido = 0.0;
        $this->usuario_id = auth()->id();
        $maxNumeroFactura = Venta::max('numero_factura');
        $nextNumeroFactura = (int)$maxNumeroFactura + 1;
        $this->numero_factura = str_pad($nextNumeroFactura, 9, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $this->buscarProducto();
        $this->buscarCliente();
        $this->buscarVenta();
        return view('livewire.venta-controller')->layout("layouts.app");
    }

    public function formatearDinero($numero)
    {
        $numero = number_format($numero, 0, ',', '.');
        return $numero;
    }

    public function calcularCambio()
    {
        // Validación: El dinero recibido no puede ser negativo
        if ($this->recibido < 0) {
            $this->addError('recibido', 'El dinero recibido no puede ser negativo.');
            $this->dispatch("errorRecibido");
            return;
        }

        var_dump($this->recibido);

        // Validación: El dinero recibido debe ser igual o mayor al total
        if ($this->recibido < $this->total) {
            $this->addError('recibido', 'El dinero recibido debe ser igual o mayor al total de la venta.');
            $this->dispatch("errorCambio");
            return;
        }

        $this->cambio = $this->recibido - $this->total;
        return true;
    }

    private function agregarProductoVenta($ventaId, $producto)
    {
        // Lógica para agregar un producto a la tabla DetalleVenta
        ProductoVenta::create([
            'venta_id' => $ventaId,
            'producto_id' => $producto['id'],
            'cantidad' => $producto['quantity'],
        ]);
    }

    private function actualizarStockProductos()
    {
        // Lógica para actualizar el stock de los productos
        foreach ($this->carrito as $producto) {
            $productoModel = Producto::find($producto['id']);
            $productoModel->stock -= $producto['quantity'];
            $productoModel->save();
        }
    }

    public function buscarCliente()
    {
        $this->clientes = Cliente::all();
        if ($this->busquedaCliente) {
            $this->clientes = Cliente::where('numero_identificacion', 'like', '%' . $this->busquedaCliente . '%')->get();
        }
    }

    public function buscarVenta()
    {
        $this->ventas = Venta::orderBy('numero_factura', 'desc')->with("cliente")->get();
        if ($this->busquedaVenta) {
            $this->ventas = Venta::where('numero_factura', 'like', '%' . $this->busquedaVenta . '%')->get();
        }
    }

    public function abrirModalRecibo()
    {
        $this->modalRecibo = true;
        $this->carritoRecibo = $this->carrito;
        $this->cliente = $this->cliente;
    }

    public function abrirModalVentas()
    {
        $this->modalVentas = true;
    }

    public function cerrarModalVentas()
    {
        $this->modalVentas = false;
    }

    #[On('cerrarModalRecibo')]
    public function cerrarModalRecibo()
    {
        $this->modalRecibo = false;
        $this->dispatch("ventaCreada");
        redirect()->to("/modulo-pos");
    }

    public function abrirModalClientes()
    {
        $this->modalClientes = true;
        $this->buscarCliente();
    }

    public function cerrarModalClientes()
    {
        $this->modalClientes = false;
        $this->busquedaCliente = "";
    }

    public function buscarProducto()
    {
        if ($this->busqueda) {
            $this->abrirModalProductos();

            // Busca productos mediante la búsqueda
            $this->productos = Producto::where('nombre', 'like', '%' . $this->busqueda . '%')
                ->orWhere('codigo', 'like', '%' . $this->busqueda . '%')->get();

            /* foreach ($this->productos as $producto) {
                $this->cantidad[$producto->id] = 10;
            } */
        } else {
            $this->cerrarModalProductos();
            $this->actualizarCarrito();
        }
    }

    public function asignarCliente($cliente)
    {
        $this->cliente = $cliente;
        $this->cerrarModalClientes();
    }

    public function agregarProductoCarrito($producto)
    {
        // Asegúrate de verificar el stock disponible si es necesario

        ["id" => $id, "nombre" => $nombre, "precio" => $precio, "codigo" => $codigo] = $producto;

        // Verifica si la cantidad solicitada está disponible en el stock del producto
        $stockDisponible = $this->verificarStockDisponible($id, $this->cantidad[$id] ?? 1);

        if (!$stockDisponible) {
            // Puedes agregar un mensaje de error o realizar otras acciones si el stock no está disponible
            // Por ejemplo, mostrar un mensaje al usuario
            $this->dispatch("errorCantidadProducto");
            return;
        }

        // Utiliza la fachada Cart para agregar el producto al carrito
        \Cart::session(auth()->id())->add([
            'id' => $id,
            'name' => $nombre,
            'price' => $precio,
            'quantity' => $this->cantidad[$id] ?? 1,
            'attributes' => [
                "codigo" => $codigo
            ],
        ]);

        // Actualiza el carrito después de agregar el producto
        $this->actualizarCarrito();

        $this->cerrarModalProductos();
    }

    public function teclaPresionada($tecla)
    {
        if ($tecla === "F2") {
            $this->abrirModalClientes();
        }
        if ($tecla === "F3") {
            $this->abrirModalVentas();
        }
        if ($tecla === "F4") {
            $this->guardarVentaEnMemoria();
        }
        if ($tecla === "F6") {
            $this->recuperarVentaDeMemoria();
        }
        if ($tecla === "Delete") {
            $this->vaciarCarrito();
        }
        if ($tecla === "PageDown") {
            $this->confirmarVenta();
        }
    }

    public function imprimirUltimaVenta()
    {
        // Obtener la última venta según el número de factura
        $ultimaVenta = Venta::orderBy('numero_factura', 'desc')->first();

        // Verificar si se encontró alguna venta
        if ($ultimaVenta) {
            // Obtener los detalles de la venta
            $ventaModel = $ultimaVenta;

            // Obtener los productos asociados a la venta
            $carritoRecibo = ProductoVenta::where('venta_id', $ultimaVenta->id)->with('producto')->get();

            // Obtener los modelos de productos relacionados con el carrito de recibo
            $productos = $carritoRecibo->map(function ($item) {
                return Producto::find($item->producto_id);
            });

            // Verificar si se encontró la venta
            if (!$ventaModel) {
                $this->dispatch("noHayVentas");
            }

            // Generar el PDF del recibo
            $pdf = PDF::loadView('livewire.recibo-venta', [
                "ventaModel" => $ventaModel,
                "carritoRecibo" => $carritoRecibo,
                "productos" => $productos
            ]);

            // Descargar el PDF del recibo con el nombre basado en el número de factura
            return $pdf->download("recibo-POS{$ventaModel->numero_factura}.pdf");
        } else {
            $this->dispatch("ultimaVenta");
        }
    }

    private function verificarStockDisponible($productoId, $cantidadSolicitada)
    {
        // Obtén el modelo del producto
        $productoModel = Producto::find($productoId);

        // Verifica si hay suficiente stock disponible
        return $productoModel->stock >= $cantidadSolicitada;
    }

    public function eliminarProductoCarrito($productoId)
    {
        \Cart::session(auth()->id())->remove($productoId);
    }

    // Métodos para abrir y cerrar modales
    public function abrirModalProductos()
    {
        $this->modalProductos = true;
    }

    // Métodos para abrir y cerrar modales
    public function cerrarModalProductos()
    {
        $this->modalProductos = false;
        $this->busqueda = "";
    }

    public function actualizarCantidad($productoId)
    {
        // Verifica si la cantidad existe antes de obtenerla
        if (isset($this->carrito[$productoId])) {
            // Obtén la nueva cantidad desde el modelo de Livewire
            $nuevaCantidad = (int)$this->carrito[$productoId]['quantity'];

            if ($nuevaCantidad != 0) {
                // Actualiza la cantidad en el carrito
                \Cart::session(auth()->id())->update($productoId, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $nuevaCantidad
                    ]
                ]);
                // Actualiza otras propiedades si es necesario
                $this->actualizarCarrito();
            }
        }
    }

    public function vaciarCarrito()
    {
        \Cart::clear();
        \Cart::session(auth()->id())->clear();
        $this->recibido = 0.0;
        $this->cambio = 0.0;
    }

    public function guardarVentaEnMemoria()
    {
        if (\Cart::session($this->usuario_id)->isEmpty()) {
            $this->dispatch("carritoVacio");
            return;
        }

        // Guarda la venta actual en memoria (en la sesión)
        session()->put(['venta_en_memoria' => [
            'cliente_id' => $this->cliente["id"] ?? null,
            'productos' => $this->carrito,
            'total' => $this->total,
        ]]);

        // Limpia el carrito después de guardar la venta en memoria
        \Cart::session($this->usuario_id)->clear();
        $this->carrito = [];
        $this->total = 0.0;
        $this->recibido = 0.0;

        $this->dispatch("enviarAMemoria");
    }

    public function recuperarVentaDeMemoria()
    {
        $ventaEnMemoria = session('venta_en_memoria');

        if ($ventaEnMemoria) {
            // Asigna los datos recuperados a las propiedades del controlador
            $this->cliente = Cliente::find($ventaEnMemoria['cliente_id']) ?? null;
            $this->carrito = $ventaEnMemoria['productos'];
            $this->total = $ventaEnMemoria['total'];



            foreach ($this->carrito as $producto) {
                // Utiliza la fachada Cart para agregar el producto al carrito
                \Cart::session(auth()->id())->add([
                    'id' => $producto->id,
                    'name' => $producto->name,
                    'price' => $producto->price,
                    'quantity' => $producto->quantity,
                    'attributes' => [
                        "codigo" => $producto->attributes->codigo
                    ],
                ]);

                // Actualiza el carrito después de agregar el producto
                $this->actualizarCarrito();
            }

            // Limpia la venta guardada en memoria después de recuperarla
            session()->forget('venta_en_memoria');

            $this->dispatch("recuperarDeMemoria");
        } else {
            $this->dispatch("noHayVentasEnMemoria");
        }
    }


    public function confirmarVenta()
    {
        if (\Cart::session($this->usuario_id)->isEmpty()) {
            $this->dispatch("carritoVacio");
            return;
        }

        $this->dispatch("confirmarVenta");
    }

    #[On('realizarVenta')]
    public function realizarVenta()
    {
        $cliente = Cliente::find(2);

        // Crea una nueva venta en la base de datos
        $venta = Venta::create([
            'usuario_id' => $this->usuario_id,
            'cliente_id' => $this->cliente["id"] ?? $cliente->id,
            'numero_factura' => $this->numero_factura,
            'total' => $this->total,
            'observaciones' => $this->observaciones,
        ]);

        // Verifica si la venta se creó correctamente
        if (!$venta) {
            $this->dispatch("errorVenta");
            return;
        }

        // Asigna la venta a $this->venta
        $this->venta = $venta;

        // Agrega productos a la tabla ProductoVenta
        foreach ($this->carrito as $producto) {
            $this->agregarProductoVenta($this->venta->id, $producto);
        }

        // Actualiza el stock de los productos
        $this->actualizarStockProductos();

        // Restablece los campos y propiedades relacionadas con la venta
        $this->observaciones = "";

        if ($this->venta) {
            $this->abrirModalRecibo();
        }

        // Limpia el carrito después de la venta
        $this->vaciarCarrito();

        $maxNumeroFactura = Venta::max('numero_factura');
        $nextNumeroFactura = (int)$maxNumeroFactura + 1;
        $this->numero_factura = str_pad($nextNumeroFactura, 9, '0', STR_PAD_LEFT);
    }

    public function generarPDFRecibo($ventaId)
    {
        // Obtener el modelo de la venta con la relación cliente cargada
        $ventaModel = Venta::with('cliente')->find($ventaId);

        // Verificar si se encontró alguna venta
        if (!$ventaModel) {
            // Manejar el caso en el que no se encontró ninguna venta
            $this->dispatch("noHayVentas");
            return; // No continúes con la generación del PDF
        }


        // Obtener los productos relacionados con el carrito de recibo
        $carritoRecibo = ProductoVenta::where('venta_id', $ventaId)
            ->with('producto') // Asegúrate de que 'producto' está definido en el modelo ProductoVenta
            ->get();

        try {
            $connector = new WindowsPrintConnector('GP-L80180');
            $printer = new Printer($connector);

            // Encabezado
            $printer->setEmphasis(true);
            $printer->text("El Rincón Verde\n");
            $printer->setEmphasis(false);
            $printer->text("Dirección: Calle 100, Fontibón\n");
            $printer->text("Teléfono: (+57) 313 210 6435\n");
            $printer->text("NIT: 123456789\n");
            $printer->text("------------------------------------------------\n");

            // Datos del documento
            $printer->text("Factura de Venta\n");
            $printer->text("Número de Factura: EPS{$ventaModel->numero_factura}\n");
            $printer->text("Fecha: {$ventaModel->created_at->format('d/m/Y H:i')}\n");
            $printer->text("------------------------------------------------\n");

            // Datos del cliente
            $printer->text("Cliente: {$ventaModel->cliente->nombre}\n");
            $printer->text("NIT Cliente: {$ventaModel->cliente->numero_identificacion}\n");
            $printer->text("------------------------------------------------\n");

            // Detalle de la compra
            $columna1 = 20; // Nombre del producto
            $columna2 = 8;  // Cantidad
            $columna3 = 10; // Precio unitario
            $columna4 = 12; // Total

            $printer->text(str_pad("Producto", $columna1) . str_pad("Cant.", $columna2) . str_pad("P. Unit", $columna3) . str_pad("Total", $columna4) . "\n");
            $printer->text(str_repeat("-", $columna1 + $columna2 + $columna3 + $columna4) . "\n");

            $subtotal = 0;

            foreach ($carritoRecibo as $item) {
                $producto = $item->producto;
                $nombreProducto = str_pad(substr($producto->nombre, 0, 19), $columna1);
                $cantidad = str_pad($item->cantidad, $columna2);
                $precioUnitario = number_format($producto->precio, 2);
                $totalItem = number_format($item->cantidad * $producto->precio, 2);

                $subtotal += $item->cantidad * $producto->precio;

                $printer->text("{$nombreProducto}{$cantidad}" . str_pad($precioUnitario, $columna3) . str_pad($totalItem, $columna4) . "\n");
            }

            // Cálculo de IVA y Total
            $ivaPorcentaje = 0.19; // 19% de IVA
            $iva = $subtotal * $ivaPorcentaje;
            $totalVenta = $subtotal + $iva;

            // Total de la venta
            $printer->text("------------------------------------------------\n");
            $printer->setEmphasis(true);
            $printer->text("Subtotal: " . number_format($subtotal, 2) . "\n");
            $printer->text("IVA (19%): " . number_format($iva, 2) . "\n");
            $printer->text("Total: " . number_format($totalVenta, 2) . "\n");
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            // Forma de pago
            $printer->text("Forma de Pago: Efectivo\n"); // Puede ser adaptado según el método de pago
            $printer->text("------------------------------------------------\n");

            // Pie de página
            $printer->text("Gracias por su compra!\n");
            $printer->text("Factura válida como comprobante de pago.\n");
            $printer->text("Para devoluciones, conserve este documento.\n");

            // Corte de papel y cierre de impresora
            $printer->cut();
            $printer->close();

            return json_encode([
                'venta' => $ventaModel,
                'carrito' => $carritoRecibo,
                'totalVenta' => $totalVenta,
            ]);
        } catch (\Exception $e) {
            // Manejar errores de impresión
            session()->flash('error', 'Error al imprimir: ' . $e->getMessage());
        }
    }





    /*     public function generarReciboTermico()
    {
        try {
            $connector = new WindowsPrintConnector('GP-L80180');
            $printer = new Printer($connector);

            // Obtén las categorías
            $categorias = Venta::all();

            // Imprimir encabezado
            $printer->text("Recibo de Categorías\n");
            $printer->text("-----------------------------\n");


            // Imprimir detalles de cada categoría
            foreach ($categorias as $categoria) {
                $printer->text("ID: {$categoria->id}\n");
                $printer->text("Nombre: {$categoria->nombre}\n");
                $printer->text("Estado: " . ($categoria->estado ? 'Activo' : 'Inactivo') . "\n");
                $printer->text("-----------------------------\n");
            }

            // Finalizar y cortar el papel
            $printer->cut();
            $printer->close();
            session()->flash('success', 'Recibo impreso correctamente.');
        } catch (\Exception $e) {
            // Manejar errores de impresión
            session()->flash('error', 'Error al imprimir: ' . $e->getMessage());
        }
    } */



    public function actualizarCarrito()
    {
        // Actualiza el contenido del carrito después de cualquier cambio
        $this->carrito = \Cart::session(auth()->id())->getContent();

        foreach ($this->carrito as $producto) {
            $producto->subtotal = $producto->quantity * $producto->price;
            $producto->subtotal = number_format($producto->subtotal, 0, ',', '.');
        }

        $this->carrito = $this->carrito->sortBy('id');

        // Calcula el total de la venta
        $this->total = $this->calcularTotal();

        // Solo actualiza el valor de recibido si no ha sido modificado por el usuario

        $this->recibido = Helpers::formatearDinero($this->total);
    }



    private function calcularTotal()
    {
        return \Cart::session(auth()->id())->getTotal();
    }
}
