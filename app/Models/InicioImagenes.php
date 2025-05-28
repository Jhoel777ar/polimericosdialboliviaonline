<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class InicioImagenes extends Model
{
    use HasFactory;

    protected $fillable = ['image_url'];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($inicioImagenes) {
            if ($inicioImagenes->image_url) {
                Storage::disk('public')->delete($inicioImagenes->image_url);
            }
        });

        static::updating(function ($inicioImagenes) {
            if ($inicioImagenes->isDirty('image_url')) {
                $originalImage = $inicioImagenes->getOriginal('image_url');
                if ($originalImage && $originalImage !== $inicioImagenes->image_url) {
                    Storage::disk('public')->delete($originalImage);
                }
            }
        });
    }
}
