<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'direccion',
    ];
}
