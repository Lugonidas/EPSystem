<?php

namespace App\Livewire;

use App\Models\ProductoVenta;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Carbon\Carbon;
use Livewire\Component;

class MostrarVentas extends Component
{

    public $ventas;
    public $busqueda = "";
    public $fechaInicio;
    public $fechaFin;

    public function render()
    {
        // Filtrar ventas por número de factura si se ingresa una búsqueda, o por rango de fechas si se ingresan fechas
        if ($this->busqueda) {
            $this->ventas = Venta::where('numero_factura', 'like', '%' . $this->busqueda . '%')->get();
        } elseif ($this->fechaInicio && $this->fechaFin) {
            $inicio = Carbon::parse($this->fechaInicio)->startOfDay();
            $fin = Carbon::parse($this->fechaFin)->endOfDay();
            $this->ventas = Venta::whereBetween('created_at', [$inicio, $fin])->orderBy('created_at', 'desc')->get();
        } else {
            // Si no hay búsqueda ni fechas, obtener todas las ventas
            $this->ventas = Venta::orderBy('created_at', 'desc')->get();
        }

        return view('livewire.mostrar-ventas')->layout("layouts.app");
    }

    public function formatearDinero($numero)
    {
        $numero = number_format($numero, 0, ',', '.');
        return $numero;
    }

    public function generarPDFVentas()
    {
        $ventas = Venta::all();
        if (!$ventas) {
            $this->dispatch("noHayVentas");
        }

        $pdf = PDF::loadView('livewire.recibo-ventas', [
            "ventas" => $ventas,
        ]);

        return $pdf->stream('ReciboVentas.pdf');
    }
    public function generarPDFRecibos($ventaId)
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

        $pdf = PDF::loadView('livewire.recibo-venta', [
            "ventaModel" => $ventaModel,
            "carritoRecibo" => $carritoRecibo
        ]);

        return $pdf->stream('ReciboVentas.pdf');
    }
}
