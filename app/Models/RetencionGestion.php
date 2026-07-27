<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetencionGestion extends Model
{
    protected $fillable = [
        'retencion_id',
        'user_id',
        'detalle',
    ];

    public function retencion()
    {
        return $this->belongsTo(Retencion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
