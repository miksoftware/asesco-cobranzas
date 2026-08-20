<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmpresaLineamiento extends Model
{
    protected $table = 'empresa_lineamientos';

    protected $fillable = ['empresa_id', 'tipo', 'concepto', 'orden', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function porcentaje(): HasOne
    {
        return $this->hasOne(EmpresaLineamientoPorcentaje::class, 'lineamiento_id');
    }

    public function tramos(): HasMany
    {
        return $this->hasMany(EmpresaLineamientoTramo::class, 'lineamiento_id')->orderBy('orden');
    }
}
