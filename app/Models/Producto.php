<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = "productos";
    protected $fillable = ["nombre", "codigo", "precio", "stock", "categoria_id", "proveedor_id", "impuesto", "unidad_medida", "imagen", "estado"];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
   
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    
}
