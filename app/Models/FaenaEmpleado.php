<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaenaEmpleado extends Model
{
    use HasUuids, SoftDeletes, Auditable;

    protected $table = 'faena_empleados';

    protected $fillable = [
        'faena_registro_id',
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
            'monto_bono' => 'decimal:2',
            'liquido_a_pagar' => 'decimal:2',
        ];
    }

    public function faenaRegistro(): BelongsTo
    {
        return $this->belongsTo(FaenaRegistro::class, 'faena_registro_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }
}
