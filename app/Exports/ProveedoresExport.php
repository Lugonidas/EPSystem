<?php

namespace App\Exports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ProveedoresExport implements FromView
{
    public function view(): View
    {
        $proveedores = Proveedor::all();

        return view('excel.proveedores', [
            'proveedores' => $proveedores
        ]);
    }
}
