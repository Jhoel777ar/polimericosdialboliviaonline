<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InicioImagenesController extends Controller
{
    public function getImages()
    {
        $images = Storage::disk('public')->files('imagenes_inicio');
        $filteredImages = array_filter($images, function ($image) {
            return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $image);
        });
        if (empty($filteredImages)) {
            return response()->json(['message' => 'No hay imágenes disponibles.'], 404);
        }
        return response()->json(array_values($filteredImages));
    }
}
