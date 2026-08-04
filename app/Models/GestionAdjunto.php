<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GestionAdjunto extends Model
{
    protected $fillable = [
        'cedula',
        'comentario',
        'archivo_path',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}