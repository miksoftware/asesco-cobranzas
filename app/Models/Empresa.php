<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tarifas(): HasMany
    {
        return $this->hasMany(EmpresaTarifa::class)->orderBy('orden');
    }

    public function canales(): HasMany
    {
        return $this->hasMany(EmpresaCanal::class)->orderBy('orden');
    }

    public function lineamientos(): HasMany
    {
        return $this->hasMany(EmpresaLineamiento::class)->orderBy('orden');
    }
}
