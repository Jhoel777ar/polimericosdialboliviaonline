<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 8);
        $offset = $request->input('offset', 0);
        $search = $request->input('search', '');
        $sort = $request->input('sort', '');

        $query = Producto::where('activo', 1)
            ->where('estado', 'disponible')
            ->with(['categoria', 'proveedor', 'color']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhereHas('categoria', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%$search%");
                    })
                    ->orWhereHas('proveedor', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%$search%");
                    });
            });
        }

        if ($sort === 'precio_asc') {
            $query->orderBy('precio', 'asc');
        } elseif ($sort === 'precio_desc') {
            $query->orderBy('precio', 'desc');
        }

        $productos = $query->offset($offset)->limit($limit)->get();
        $totalProductos = $query->count();

        return response()->json([
            'productos' => $productos,
            'total' => $totalProductos
        ]);
    }
}
