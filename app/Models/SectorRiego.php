<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectorRiego extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'sectores_riego';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'caudal_disponible_l_s',
    ];

    protected function casts(): array
    {
        return [
            'caudal_disponible_l_s' => 'decimal:2',
        ];
    }
}
