<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresupuestoDetalle extends Model
{
    use HasUuids, Auditable;

    protected $table = 'presupuesto_detalles';

    protected $fillable = [
        'presupuesto_id',
        'cuartel_id',
        'actividad_id',
        'estimacion_id',
        'contenedor_cosecha_id',
        'rendimiento_promedio',
        'hectareas',
        'n_plantas',
        'kilos_estimados',
        'jh_totales',
        'valor_unitario',
        'valor_total',
        'anho_fiscal',
        'mes',
    ];

    protected function casts(): array
    {
        return [
            'rendimiento_promedio' => 'decimal:2',
            'hectareas' => 'decimal:2',
            'n_plantas' => 'integer',
            'kilos_estimados' => 'decimal:2',
            'jh_totales' => 'decimal:2',
            'valor_unitario' => 'decimal:2',
            'valor_total' => 'decimal:2',
            'anho_fiscal' => 'integer',
            'mes' => 'integer',
        ];
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_id');
    }

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class);
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function estimacion(): BelongsTo
    {
        return $this->belongsTo(Estimacion::class);
    }

    public function contenedorCosecha(): BelongsTo
    {
        return $this->belongsTo(ContenedorCosecha::class);
    }
}
