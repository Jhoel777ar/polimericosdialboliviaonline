<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soporte extends Model
{
    protected $fillable = [
        'user_id',
        'consultation_date',
        'subject',
        'description',
        'status',
        'respuesta',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
