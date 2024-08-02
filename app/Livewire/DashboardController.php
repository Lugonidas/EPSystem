<?php

namespace App\Livewire;

use App\Models\Venta;
use App\Services\DashboardService;
use Livewire\Component;

class DashboardController extends Component
{
    public $totalUltimaVenta;
    public $totalVentasDelDia;
    public $totalVentasDelMes;
    public $totalVentasDeLaSemana;
    public $productosMasVendidos;
    public $comportamientoVentas;
    public $clientesQueMasCompran;
    public $ventas;
    public $ventasDiarias;
    public $horaActual;

    protected $dashboardService;

    public function mount(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;

        $this->totalUltimaVenta = $this->dashboardService->totalUltimaVenta();
        $this->totalVentasDelDia = $this->dashboardService->totalVentasDelDia();
        $this->totalVentasDeLaSemana = $this->dashboardService->totalVentasDeLaSemana();
        $this->totalVentasDelMes = $this->dashboardService->totalVentasDelMes();
        $this->productosMasVendidos = $this->dashboardService->productosMasVendidos();
        $this->comportamientoVentas = $this->dashboardService->comportamientoVentas();
        $this->clientesQueMasCompran = $this->dashboardService->clientesQueMasCompran();
        $this->ventasDiarias = $this->dashboardService->ventasDiarias();
        $this->horaActual = $this->dashboardService->horaActual();
        $this->ventas = Venta::all();
    }

    public function render()
    {
        $this->dispatch('ventasDiariasUpdated');

        return view('livewire.dashboard-controller')->layout("layouts.app");
    }

    public function formatearDinero($numero)
    {
        return $this->dashboardService->formatearDinero($numero);
    }
}
