<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroJuridicoHistory extends Model
{
    protected $fillable = [
        'cobro_juridico_id',
        'user_id',
        'seccion',
        'accion',
        'campo',
        'valor_anterior',
        'valor_nuevo',
    ];

    public function cobroJuridico()
    {
        return $this->belongsTo(CobroJuridico::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
