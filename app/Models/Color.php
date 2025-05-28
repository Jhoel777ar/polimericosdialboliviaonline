<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['color'];
    public function productos()
    {
        return $this->hasMany(Producto::class, 'color_id');
    }
}
