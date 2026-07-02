<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborEmpleado extends Model
{
    use HasUuids, SoftDeletes, Auditable;

    protected $table = 'labor_empleados';

    protected $fillable = [
        'labor_id',
        'empleado_id',
        'horas_trabajadas',
        'cantidad_unidades_producidas',
        'valor_trato_unitario',
        'monto_bono',
        'liquido_a_pagar',
        'sync_id',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'horas_trabajadas' => 'decimal:2',
            'cantidad_unidades_producidas' => 'decimal:2',
            'valor_trato_unitario' => 'decimal:2',
            'monto_bono' => 'decimal:2',
            'liquido_a_pagar' => 'decimal:2',
        ];
    }

    public function labor(): BelongsTo
    {
        return $this->belongsTo(Labor::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }
}
