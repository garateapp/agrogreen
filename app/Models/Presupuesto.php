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

class Presupuesto extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = [
        'tenant_id',
        'temporada_id',
        'anho_fiscal',
        'mes',
        'estado',
        'tipo_cambio_usd',
    ];

    protected function casts(): array
    {
        return [
            'anho_fiscal' => 'integer',
            'mes' => 'integer',
            'tipo_cambio_usd' => 'decimal:2',
        ];
    }

    public function temporada(): BelongsTo
    {
        return $this->belongsTo(Temporada::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PresupuestoDetalle::class, 'presupuesto_id');
    }
}
