<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuartel extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = [
        'tenant_id',
        'centro_costo_id',
        'nombre',
        'superficie_hectareas',
        'especie_id',
        'ano_plantacion',
        'distancia_sobre_hilera',
        'distancia_intra_hilera',
        'geometria_geojson',
    ];

    protected function casts(): array
    {
        return [
            'superficie_hectareas' => 'decimal:2',
            'geometria_geojson' => 'json',
        ];
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function especie(): BelongsTo
    {
        return $this->belongsTo(Especie::class);
    }

    public function variedades(): BelongsToMany
    {
        return $this->belongsToMany(Variedad::class, 'cuartel_variedad')
            ->withPivot('cantidad_plantas')
            ->withTimestamps();
    }

    public function cosechas(): HasMany
    {
        return $this->hasMany(Cosecha::class, 'cuartel_id');
    }
}
