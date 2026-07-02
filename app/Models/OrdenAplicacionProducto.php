<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenAplicacionProducto extends Model
{
    use HasUuids, SoftDeletes, Auditable;

    protected $table = 'orden_aplicacion_productos';

    protected $fillable = [
        'orden_aplicacion_id',
        'producto_id',
        'dosis_comercial_por_hl',
        'cantidad_total_insumo',
    ];

    public function ordenAplicacion(): BelongsTo
    {
        return $this->belongsTo(OrdenAplicacion::class, 'orden_aplicacion_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
