<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService
{
    public function totalUltimaVenta(): float
    {
        $ultimaVenta = Venta::orderBy('numero_factura', 'desc')->with("cliente")->first();
        return $ultimaVenta->total ?? 0.0;
    }

    public function totalVentasDelDia(): float
    {
        $fechaActual = Carbon::now()->toDateString();
        return Venta::whereDate('created_at', $fechaActual)->sum('total') ?? 0.0;
    }

    public function totalVentasDeLaSemana(): float
    {
        $fechaActual = Carbon::now();
        $inicioSemana = $fechaActual->copy()->startOfWeek(Carbon::MONDAY);
        return Venta::whereBetween('created_at', [$inicioSemana, $fechaActual])->sum('total') ?? 0.0;
    }

    public function totalVentasDelMes(): float
    {
        $fechaActual = Carbon::now();
        $inicioMes = $fechaActual->copy()->startOfMonth();
        return Venta::whereBetween('created_at', [$inicioMes, $fechaActual])->sum('total') ?? 0.0;
    }

    public function horaActual(): string
    {
        return Carbon::now()->toTimeString();
    }

    public function ComportamientoVentas(): array
    {
        $ventasPorFecha = Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $comportamientoVentasData = [];
        foreach ($ventasPorFecha as $venta) {
            $comportamientoVentasData[] = [
                'fecha' => (new \DateTime($venta->fecha))->format('Y-m-d'),
                'ventas' => $venta->ventas,
            ];
        }

        return $comportamientoVentasData;
    }

    public function productosMasVendidos(): Collection
    {
        return ProductoVenta::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get();
    }

    public function clientesQueMasCompran(): Collection
    {
        return Cliente::select('clientes.*', DB::raw('COUNT(ventas.id) as total_compras'))
            ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
            ->groupBy('clientes.id')
            ->orderByDesc('total_compras')
            ->limit(3)
            ->get();
    }

    public function ventasDiarias(): Collection
    {
        $fechaActual = Carbon::now()->toDateString();
        return Venta::whereDate('created_at', $fechaActual)->get();
    }

    public function formatearDinero($numero): string
    {
        return number_format($numero, 0, ',', '.');
    }
}
