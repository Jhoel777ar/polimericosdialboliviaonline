<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Producto;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $start = max(0, (int)$request->input('start', 0));
        $limit = max(1, (int)$request->input('limit', 4));
        $categorias = Categoria::where('activo', 1)
            ->with('parent')
            ->skip($start)
            ->take($limit)
            ->get();
        $totalCategorias = Categoria::where('activo', 1)->count();
        return response()->json([
            'categories' => $categorias->isEmpty() ? [] : $categorias,
            'total' => $totalCategorias,
            'message' => $categorias->isEmpty() ? 'No hay categorías disponibles.' : null
        ], 200);
    }
    
    //ver prodcutos por categoria
    public function show(Request $request, $id)
    {
        try {
            $categoria = Categoria::with(['parent', 'children:id'])->findOrFail($id);
            $categoriaIds = collect([$categoria->id]);
            if ($categoria->children->isNotEmpty()) {
                $categoriaIds = $categoriaIds->merge($categoria->children->pluck('id'));
            }
            $productos = Producto::whereIn('categoria_id', $categoriaIds)
                ->where('activo', 1)
                ->with(['color', 'proveedor'])
                ->paginate(8);

            return Inertia::render('Categorias/Ver', [
                'categoria' => $categoria,
                'productos' => $productos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo cargar la categoría: ' . $e->getMessage()
            ], 404);
        }
    }
}
