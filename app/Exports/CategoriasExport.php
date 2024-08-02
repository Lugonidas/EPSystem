<?php

namespace App\Exports;

use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class CategoriasExport implements FromView
{
    public function view(): View
    {
        $categorias = Categoria::all();

        return view('excel.categorias', [
            'categorias' => $categorias
        ]);
    }
}