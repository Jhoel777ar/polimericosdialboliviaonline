<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VentaLocal extends Model
{
    
    use HasFactory;

    protected $table = 'venta_locals';

    protected $fillable = [
        'nombre_usuario',
        'telefono',
        'CI',
        'cantidad',
        'fecha_venta',
        'total',
        'estado',
        'detalle_compra',
    ];
    protected $casts = [
        'detalle_compra' => 'array', 
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
    ];
}
