<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContenedorCosecha extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'contenedores_cosecha';

    protected $fillable = [
        'tenant_id',
        'especie_id',
        'nombre',
        'unidades_por_bin',
        'peso_bin_kg',
    ];

    protected function casts(): array
    {
        return [
            'unidades_por_bin' => 'integer',
            'peso_bin_kg' => 'decimal:2',
        ];
    }

    public function especie(): BelongsTo
    {
        return $this->belongsTo(Especie::class);
    }

    public function pesoUnitario(): Attribute
    {
        return Attribute::get(function () {
            if ($this->unidades_por_bin && $this->peso_bin_kg) {
                return round($this->peso_bin_kg / $this->unidades_por_bin, 3);
            }
            return null;
        });
    }
}
