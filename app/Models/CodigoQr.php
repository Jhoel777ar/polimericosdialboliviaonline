<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class CodigoQr extends Model
{
    use HasFactory;

    protected $fillable = ['qr_image_url', 'monto_aceptado', 'fecha_expiracion'];
    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($codigoQr) {
            if ($codigoQr->qr_image_url) {
                Storage::disk('public')->delete($codigoQr->qr_image_url);
            }
        });
        static::updating(function ($codigoQr) {
            if ($codigoQr->isDirty('qr_image_url')) {
                $originalImage = $codigoQr->getOriginal('qr_image_url');
                if ($originalImage && $originalImage !== $codigoQr->qr_image_url) {
                    Storage::disk('public')->delete($originalImage);
                }
            }
        });
    }
}
