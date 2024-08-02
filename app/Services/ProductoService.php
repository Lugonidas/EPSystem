<?php


namespace App\Services;

use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoService
{
    public function getAllProducts()
    {
        return Producto::all();
    }

    public function searchProducts($search)
    {
        return Producto::where('nombre', 'like', '%' . $search . '%')
            ->orWhere('codigo', 'like', '%' . $search . '%')
            ->get();
    }

    public function createProduct($data)
    {
        $nombreImagen = 'user-default.png';
        if (isset($data['imagen'])) {
            $nombreImagen = md5(uniqid()) . '.' . $data['imagen']->extension();
            $data['imagen']->storeAs('uploads/imagenes_productos', $nombreImagen, 'public');
        }

        $data['imagen'] = $nombreImagen;
        return Producto::create($data);
    }

    public function updateProduct($id, $data)
    {
        $producto = Producto::find($id);
        if (isset($data['imagen_nueva'])) {
            if ($producto->imagen && $producto->imagen !== 'user-default.png' && Storage::disk('public')->exists('uploads/imagenes_productos/' . $producto->imagen)) {
                Storage::disk('public')->delete('uploads/imagenes_productos/' . $producto->imagen);
            }
            $nombreImagen = md5(uniqid()) . '.' . $data['imagen_nueva']->extension();
            $data['imagen_nueva']->storeAs('uploads/imagenes_productos', $nombreImagen, 'public');
            $data['imagen'] = $nombreImagen;
        } else {
            $data['imagen'] = $producto->imagen;
        }

        $producto->update($data);
        return $producto;
    }

    public function deleteProduct($id)
    {
        Producto::find($id)->delete();
    }

    public function generatePDF()
    {
        $productos = Producto::all();
        return Pdf::loadView('pdf.recibo-productos', ['productos' => $productos])->stream('ReciboProductos.pdf');
    }
}