<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tercero extends Model
{
    protected $fillable = [
        'referencia',
        'cedula_tercero',
        'nombre_tercero',
        'calidad',
        'empresa',
        'dato',
        'tipo_dato',
        'notificar',
        'fuente',
        'modified_by',
        'modified_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'notificar'   => 'boolean',
            'modified_at' => 'datetime',
        ];
    }

    public function modifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con comentarios: todos los comentarios cuya cédula
     * coincida con la referencia (cédula titular) de este tercero.
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'cedula', 'referencia');
    }

    /**
     * Scope: filtrar por referencia (cédula titular).
     */
    public function scopeByReferencia($query, string $referencia)
    {
        return $query->where('referencia', $referencia);
    }

    /**
     * Scope: filtrar por cédula del tercero.
     */
    public function scopeByCedulaTercero($query, string $cedula)
    {
        return $query->where('cedula_tercero', $cedula);
    }
}
