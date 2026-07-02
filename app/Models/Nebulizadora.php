<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nebulizadora extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'nebulizadoras';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'patente',
        'capacidad_litros',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'capacidad_litros' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }
}
