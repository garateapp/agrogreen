<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarjetaAsignacion extends Model
{
    use HasUuids;

    protected $table = 'tarjeta_asignaciones';

    protected $fillable = [
        'tarjeta_id',
        'empleado_id',
        'fecha_asignacion',
        'fecha_desasignacion',
        'asignado_por',
        'desasignado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_desasignacion' => 'datetime',
        ];
    }

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }
}
