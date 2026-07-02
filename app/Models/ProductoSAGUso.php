<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoSAGUso extends Model
{
    use HasUuids, Auditable;

    protected $table = 'producto_sag_usos';

    protected $fillable = [
        'producto_sag_id',
        'categoria_id',
        'objetivo',
        'dosis_min',
        'dosis_max',
        'unidad_dosis',
        'carencia_dias',
        'reingreso_horas',
        'restricciones',
    ];

    protected function casts(): array
    {
        return [
            'dosis_min' => 'decimal:4',
            'dosis_max' => 'decimal:4',
            'carencia_dias' => 'integer',
            'reingreso_horas' => 'integer',
        ];
    }

    public function productoSAG(): BelongsTo
    {
        return $this->belongsTo(ProductoSAG::class, 'producto_sag_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
