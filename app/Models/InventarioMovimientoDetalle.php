<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioMovimientoDetalle extends Model
{
    use HasUuids, Auditable;

    protected $table = 'inventario_movimiento_detalles';

    protected $fillable = [
        'movimiento_id',
        'producto_id',
        'lote_id',
        'cantidad',
        'costo_unitario_moneda_base',
        'saldo_stock_anterior',
        'saldo_stock_posterior',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:4',
            'costo_unitario_moneda_base' => 'decimal:4',
            'saldo_stock_anterior' => 'decimal:4',
            'saldo_stock_posterior' => 'decimal:4',
        ];
    }

    public function movimiento(): BelongsTo
    {
        return $this->belongsTo(InventarioMovimiento::class, 'movimiento_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
