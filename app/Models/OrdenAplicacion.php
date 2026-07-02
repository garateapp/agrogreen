<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenAplicacion extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'ordenes_aplicacion';

    protected $fillable = [
        'tenant_id',
        'fecha_planificada',
        'estado',
        'mojamiento_l_ha',
        'tractor_id',
        'nebulizadora_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_planificada' => 'date',
        ];
    }

    public function ordenAplicacionProductos(): HasMany
    {
        return $this->hasMany(OrdenAplicacionProducto::class, 'orden_aplicacion_id');
    }

    public function ordenAplicacionCuarteles(): HasMany
    {
        return $this->hasMany(OrdenAplicacionCuartel::class, 'orden_aplicacion_id');
    }
}
