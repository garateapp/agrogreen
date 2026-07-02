<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipoAplicacion extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'equipos_aplicacion';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'tipo',
        'ultima_calibracion',
        'proxima_calibracion',
        'ultima_mantencion',
        'proxima_mantencion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'ultima_calibracion' => 'date',
            'proxima_calibracion' => 'date',
            'ultima_mantencion' => 'date',
            'proxima_mantencion' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function getCalibracionVigenteAttribute(): bool
    {
        return $this->proxima_calibracion === null || $this->proxima_calibracion >= now();
    }

    public function getMantencionVigenteAttribute(): bool
    {
        return $this->proxima_mantencion === null || $this->proxima_mantencion >= now();
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
