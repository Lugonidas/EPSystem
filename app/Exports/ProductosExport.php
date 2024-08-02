<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ProductosExport implements FromView
{
    public function view(): View
    {
        $productos = Producto::all();

        return view('excel.productos', [
            'productos' => $productos
        ]);
    }
}
