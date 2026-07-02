<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egreso extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'egresos';

    protected $fillable = [
        'tenant_id',
        'orden_compra_id',
        'tipo_origen',
        'proveedor_id',
        'centro_costo_id',
        'item_gasto_id',
        'tipo_documento',
        'folio_documento',
        'fecha_registro',
        'moneda',
        'tipo_cambio_egreso',
        'monto_total_moneda_base',
        'estado_pago',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'date',
            'tipo_cambio_egreso' => 'decimal:4',
            'monto_total_moneda_base' => 'decimal:2',
        ];
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function itemGasto(): BelongsTo
    {
        return $this->belongsTo(ItemGasto::class, 'item_gasto_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'egreso_id');
    }
}
