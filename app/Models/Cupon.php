<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descuento',
        'porcentaje',
        'uso_maximo',
        'usos_actuales',
        'fecha_expiracion',
        'activo',
    ];
    public function esValido(): bool
    {
        return $this->activo &&
            ($this->uso_maximo > $this->usos_actuales) &&
            (is_null($this->fecha_expiracion) || $this->fecha_expiracion > now());
    }
}
