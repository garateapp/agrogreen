<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'lotes';

    protected $fillable = [
        'tenant_id',
        'bodega_id',
        'producto_id',
        'codigo_lote',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_disponible',
        'costo_unitario',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'cantidad_inicial' => 'decimal:4',
            'cantidad_disponible' => 'decimal:4',
            'costo_unitario' => 'decimal:4',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }

    public function scopeConStock(Builder $query): Builder
    {
        return $query->where('cantidad_disponible', '>', 0);
    }

    public function scopeNoVencido(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('fecha_vencimiento')
              ->orWhere('fecha_vencimiento', '>=', now()->startOfDay());
        });
    }

    public function scopeDisponible(Builder $query): Builder
    {
        return $query->conStock()->noVencido();
    }
}
