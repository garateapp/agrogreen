<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'goods_receipts';

    protected $fillable = [
        'tenant_id',
        'numero',
        'fecha_emision',
        'descripcion',
        'tipo',
        'proveedor_id',
        'distribuir_costos',
        'descuento_linea',
        'vencimiento_lote',
        'lineas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'distribuir_costos' => 'boolean',
            'descuento_linea' => 'boolean',
            'vencimiento_lote' => 'boolean',
            'lineas' => 'array',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}
