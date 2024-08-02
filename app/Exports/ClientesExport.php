<?php

namespace App\Exports;


use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ClientesExport implements FromView
{
    public function view(): View
    {
        $clientes = Cliente::all();

        return view('excel.clientes', [
            'clientes' => $clientes
        ]);
    }
}
