<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VentaEliminada extends Model
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
        'detalles_venta',
    ];

    protected $casts = [
        'detalles_venta' => 'array', 
    ];
}
