<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = "clientes";
    protected $fillable = ["nombre", "numero_identificacion", "celular", "email", "ciudad", "barrio", "direccion", "imagen"];

    // Relación con las ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
}