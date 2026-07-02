<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jornada extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'jornadas';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'horas_jornada',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'horas_jornada' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }
}
