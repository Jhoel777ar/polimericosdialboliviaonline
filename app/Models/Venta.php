<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'ID_Usuario',
        'Cantidad',
        'Fecha_Venta',
        'Total',
        'Totalcondescuento',
        'Estado',
        'Método_Pago',
        'tipo_entrega',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ID_Usuario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto');
    }

    public function envios()
    {
        return $this->hasOne(Envio::class, 'ID_Venta');
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function comprobante_pago()
    {
        return $this->hasMany(ComprobantePago::class, 'ID_Venta');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($venta) {
            try {
                $ventaEliminada = new \App\Models\VentaEliminada([
                    'ID_Usuario' => $venta->ID_Usuario,
                    'Cantidad' => $venta->Cantidad,
                    'Fecha_Venta' => $venta->Fecha_Venta,
                    'Total' => $venta->Total,
                    'Totalcondescuento' => $venta->Totalcondescuento,
                    'Estado' => $venta->Estado,
                    'Método_Pago' => $venta->Método_Pago,
                    'tipo_entrega' => $venta->tipo_entrega,
                    'detalles_venta' => $venta->detalles->toArray(),
                    'error_detalle' => null,
                ]);
                $ventaEliminada->save();
                foreach ($venta->detalles as $detalle) {
                    $producto = $detalle->producto;
                    if ($producto) {
                        $producto->stock += $detalle->cantidad;
                        $producto->save();
                    } else {
                        $ventaEliminada->error_detalle = 'Producto no encontrado: ' . $detalle->producto_id;
                        $ventaEliminada->save();
                    }
                }
            } catch (\Exception $e) {
                $ventaEliminada->error_detalle = 'Error en el proceso de eliminación: ' . $e->getMessage();
                $ventaEliminada->save();
            }
        });
    }
}
