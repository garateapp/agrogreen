<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trato extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'tratos';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'codigo',
        'tipo_trato',
        'unidad_medida',
        'no_agrupar_actividad',
        'depende_jornada',
        'sustraer_trato_base',
        'bonificacion',
        'hora_extra',
        'no_enviar_integraciones',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'no_agrupar_actividad' => 'boolean',
            'depende_jornada' => 'boolean',
            'sustraer_trato_base' => 'boolean',
            'bonificacion' => 'boolean',
            'hora_extra' => 'boolean',
            'no_enviar_integraciones' => 'boolean',
            'activo' => 'boolean',
        ];
    }
}
