<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EpsSystem extends Model
{
    protected $fillable = [
        'name', 'slug', 'base_url', 'api_token',
        'endpoint_path', 'timeout', 'is_active', 'order',
    ];

    protected $hidden = ['api_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'api_token' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (EpsSystem $system) {
            if (empty($system->slug)) {
                $system->slug = Str::slug($system->name);
            }
        });
    }

    public function buildUrl(string $cedula): string
    {
        $path = str_replace('{cedula}', $cedula, $this->endpoint_path);

        return rtrim($this->base_url, '/') . $path;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
