<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory;
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'stock',
        'imagen_url',
        'estado',
        'proveedor_id',
        'color_id',
        'activo',
        'precio_estudiante',
        'precio_proveedor',
    ];
    use SoftDeletes;
    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($producto) {
            if ($producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
        });
        static::updating(function ($producto) {
            if ($producto->isDirty('imagen_url')) {
                $originalImage = $producto->getOriginal('imagen_url');
                if ($originalImage && $originalImage !== $producto->imagen_url) {
                    Storage::disk('public')->delete($originalImage);
                }
            }
        });
    }
}
