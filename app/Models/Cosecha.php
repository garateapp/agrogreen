<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Cosecha extends Model
{
    use BelongsToTenant, HasUuids, Auditable;

    protected $fillable = [
        'tenant_id',
        'fecha_hora',
        'cuartel_id',
        'empleado_id',
        'jefe_cosecha_id',
        'contenedor_id',
        'codigo_tarjeta_qr',
        'peso_bruto',
        'peso_tara',
        'peso_neto',
        'sync_id',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'peso_bruto' => 'decimal:3',
            'peso_tara' => 'decimal:3',
            'peso_neto' => 'decimal:3',
        ];
    }

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function contenedor(): BelongsTo
    {
        return $this->belongsTo(ContenedorCosecha::class, 'contenedor_id');
    }

    public function jefeCosecha(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_cosecha_id');
    }
}
