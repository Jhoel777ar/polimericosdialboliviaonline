<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ComprobantePago extends Model
{
    use HasFactory;

    protected $fillable = ['ID_Venta', 'image_url', 'Fecha_Subida', 'monto_reportado', 'description', 'estado'];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta');
    }
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($comprobantePago) {
            if ($comprobantePago->image_url) {
                Storage::disk('public')->delete($comprobantePago->image_url);
            }
        });
    }
}
