<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsuariosExport implements FromView
{
    public function view(): View
    {
        $usuarios = User::all();

        return view('excel.usuarios', [
            'usuarios' => $usuarios
        ]);
    }
}
