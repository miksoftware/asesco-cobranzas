<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetencionHistory extends Model
{
    protected $fillable = [
        'retencion_id',
        'user_id',
        'seccion',
        'accion',
        'campo',
        'valor_anterior',
        'valor_nuevo',
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
