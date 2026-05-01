<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comentario extends Model
{
    protected $fillable = [
        'fecha',
        'hora',
        'gestor',
        'comentario',
        'canal',
        'tipo_contacto',
        'efecto_gestion',
        'accion_cobro',
        'cedula',
        'nombre',
        'empresa',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con terceros: todos los terceros cuya referencia (cédula titular)
     * coincida con la cédula de este comentario.
     */
    public function terceros(): HasMany
    {
        return $this->hasMany(Tercero::class, 'referencia', 'cedula');
    }
}
