<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = [
        'ID_Usuario',
        'ID_Producto',
        'Cantidad',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ID_Usuario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto');
    }
}
