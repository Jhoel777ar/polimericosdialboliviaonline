<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Envio extends Model
{
    use HasFactory;
    protected $fillable = [
        'ID_Venta',
        'Empresa_Envio',
        'Numero_Guia',
        'Dirrecion',
        'Fecha_Envio',
        'Estado_Envio',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($envio) {
            if ($envio->getOriginal('Estado_Envio') !== $envio->Estado_Envio) {
                $venta = $envio->venta;

                if ($envio->Estado_Envio === 'entregado') {
                    if (in_array($venta->Estado, ['pendiente', 'pagado', 'enviado'])) {
                        $venta->Estado = 'entregado';
                        $venta->save();
                    }
                } elseif ($envio->Estado_Envio === 'en tránsito') {
                    if (in_array($venta->Estado, ['pendiente', 'pagado'])) {
                        $venta->Estado = 'enviado';
                        $venta->save();
                    }
                }
            }
        });
    }
}
