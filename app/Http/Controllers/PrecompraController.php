<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Producto;

class PrecompraController extends Controller
{
    public function show($id)
    {
        try {
            $producto = Producto::with([
                'categoria:id,nombre,parent_id',
                'categoria.parent:id,nombre',
                'proveedor:id,nombre',
                'color:id,color'
            ])->findOrFail($id);

            $productosRelacionados = Producto::with([
                'categoria:id,nombre',
                'proveedor:id,nombre',
                'color:id,color'
            ])
                ->where('categoria_id', $producto->categoria_id)
                ->where('id', '!=', $producto->id)
                ->get();

            return Inertia::render('Ventas/Precompra', [
                'producto' => $producto,
                'productosRelacionados' => $productosRelacionados,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Ocurrió un problema al cargar el producto: ' . $e->getMessage()
            ]);
        }
    }
}
