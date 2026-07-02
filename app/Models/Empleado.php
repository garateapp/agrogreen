<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = [
        'tenant_id',
        'rut',
        'nombre',
        'apellido',
        'tipo_contrato',
        'contratista_id',
        'es_contratista',
        'valor_dia_base',
        'valor_hora_extra',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'valor_dia_base' => 'decimal:2',
            'valor_hora_extra' => 'decimal:2',
            'es_contratista' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function faenaEmpleados(): HasMany
    {
        return $this->hasMany(FaenaEmpleado::class);
    }

    public function contratista(): BelongsTo
    {
        return $this->belongsTo(Contratista::class);
    }

    public function tarjetas(): HasMany
    {
        return $this->hasMany(Tarjeta::class);
    }
}
