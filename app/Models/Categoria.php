<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Categoria extends Model
{
    use HasFactory;
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_url',
        'activo',
        'parent_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($categoria) {
            if ($categoria->imagen_url) {
                Storage::disk('public')->delete($categoria->imagen_url);
            }
        });
        static::updating(function ($categoria) {
            if ($categoria->isDirty('imagen_url')) {
                $originalImage = $categoria->getOriginal('imagen_url');
                if ($originalImage && $originalImage !== $categoria->imagen_url) {
                    Storage::disk('public')->delete($originalImage);
                }
            }
        });
    }
}
