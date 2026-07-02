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

class FaenaRegistro extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'faenas_registro';

    protected $fillable = [
        'tenant_id',
        'fecha',
        'actividad_id',
        'centro_costo_id',
        'supervisor_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function faenaEmpleados(): HasMany
    {
        return $this->hasMany(FaenaEmpleado::class, 'faena_registro_id');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function cuarteles(): BelongsToMany
    {
        return $this->belongsToMany(Cuartel::class, 'faena_registro_cuartel', 'faena_registro_id', 'cuartel_id')
            ->withTimestamps();
    }
}
