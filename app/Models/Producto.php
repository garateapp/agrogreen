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

class Producto extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'codigo_barras',
        'categoria',
        'unidad_medida_id',
        'ingrediente_activo',
        'dosis_recomendada_por_ha',
        'dias_carencia',
    ];

    protected function casts(): array
    {
        return [
            'dosis_recomendada_por_ha' => 'decimal:2',
            'dias_carencia' => 'integer',
        ];
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_medida_id');
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'producto_id');
    }

    public function productoSAG(): HasMany
    {
        return $this->hasMany(ProductoSAG::class, 'producto_id');
    }
}
