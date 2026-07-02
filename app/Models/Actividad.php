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

class Actividad extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'actividades';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'codigo',
        'icono',
        'color',
        'tipo_labor',
        'unidad_medida_id',
        'valor',
        'requiere_maquinaria',
        'presupuestable',
        'item_gasto_id',
    ];

    protected function casts(): array
    {
        return [
            'requiere_maquinaria' => 'boolean',
            'presupuestable' => 'boolean',
            'valor' => 'decimal:2',
        ];
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_medida_id');
    }

    public function itemGasto(): BelongsTo
    {
        return $this->belongsTo(ItemGasto::class);
    }

    public function faenasRegistros(): HasMany
    {
        return $this->hasMany(FaenaRegistro::class, 'actividad_id');
    }
}
