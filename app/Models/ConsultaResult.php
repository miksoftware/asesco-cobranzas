<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaResult extends Model
{
    protected $fillable = [
        'cedula', 'eps_system_id', 'user_id',
        'data', 'found', 'error',
    ];

    protected function casts(): array
    {
        return [
            'data'  => 'array',
            'found' => 'boolean',
        ];
    }

    public function epsSystem(): BelongsTo
    {
        return $this->belongsTo(EpsSystem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
