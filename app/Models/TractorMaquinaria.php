<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TractorMaquinaria extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'tractores_maquinaria';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'patente_o_identificador',
        'tipo',
        'horas_motor_iniciales',
        'consumo_estimado_combustible_hora',
    ];

    protected function casts(): array
    {
        return [
            'horas_motor_iniciales' => 'decimal:2',
            'consumo_estimado_combustible_hora' => 'decimal:2',
        ];
    }
}
