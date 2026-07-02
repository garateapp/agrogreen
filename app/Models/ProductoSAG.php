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

class ProductoSAG extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'productos_sag';

    protected $fillable = [
        'tenant_id',
        'producto_id',
        'clasificacion_agroquimico_id',
        'nro_autorizacion_sag',
        'nombre_comercial',
        'ingrediente_activo',
        'titular',
        'estado_sag',
        'toxicidad_abejas',
        'url_etiqueta',
        'url_hds',
        'ultima_actualizacion_sag',
    ];

    protected function casts(): array
    {
        return [
            'estado_sag' => 'string',
            'toxicidad_abejas' => 'string',
            'ultima_actualizacion_sag' => 'date',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function clasificacionAgroquimico(): BelongsTo
    {
        return $this->belongsTo(ClasificacionAgroquimico::class, 'clasificacion_agroquimico_id');
    }

    public function usos(): HasMany
    {
        return $this->hasMany(ProductoSAGUso::class, 'producto_sag_id');
    }
}
