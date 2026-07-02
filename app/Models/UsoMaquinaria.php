<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsoMaquinaria extends Model
{
    use BelongsToTenant, HasUuids, Auditable;

    protected $table = 'uso_maquinaria';

    protected $fillable = [
        'tenant_id',
        'tractor_id',
        'operador_id',
        'faena_registro_id',
        'fecha',
        'horas_inicio',
        'horas_fin',
        'horas_totales',
        'centro_costo_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'horas_inicio' => 'decimal:2',
            'horas_fin' => 'decimal:2',
            'horas_totales' => 'decimal:2',
        ];
    }

    public function tractor(): BelongsTo
    {
        return $this->belongsTo(TractorMaquinaria::class, 'tractor_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'operador_id');
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class);
    }

    public function faenaRegistro(): BelongsTo
    {
        return $this->belongsTo(FaenaRegistro::class);
    }

    public function consumos(): HasMany
    {
        return $this->hasMany(ConsumoMaquinaria::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            if ($model->horas_fin !== null && $model->horas_inicio !== null) {
                $model->horas_totales = max(0, round((float) $model->horas_fin - (float) $model->horas_inicio, 2));
            }
        });
    }
}
