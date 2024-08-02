<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = "ventas";
    protected $fillable = [
        'cliente_id',
        'total',    
        'observaciones',
        'numero_factura',
    ];

    // Relación con el cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function productosVenta()
    {
        return $this->hasMany(ProductoVenta::class, 'venta_id');
    }
}
