<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Bodega;
use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class InventarioMovimiento extends Model
{
    use BelongsToTenant, HasUuids, Auditable;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'tenant_id',
        'codigo',
        'bodega_origen_id',
        'bodega_destino_id',
        'tipo_movimiento',
        'documento_referencia_id',
        'fecha_movimiento',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_movimiento' => 'datetime',
        ];
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(InventarioMovimientoDetalle::class, 'movimiento_id');
    }

    public function bodegaOrigen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_origen_id');
    }

    public function bodegaDestino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_destino_id');
    }
}
