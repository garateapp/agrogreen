<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aplicador extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'aplicadores';

    protected $fillable = [
        'tenant_id',
        'nombres',
        'apellidos',
        'rut',
        'fecha_nacimiento',
        'capacitado',
        'certificado_url',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'capacitado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento?->age ?? 0;
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeCapacitado(Builder $query): Builder
    {
        return $query->where('capacitado', true);
    }
}
