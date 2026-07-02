<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimacion extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'estimaciones';

    protected $fillable = [
        'tenant_id',
        'cuartel_id',
        'anho',
        'nombre',
        'kilos_estimados',
        'fecha_estimacion',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'anho' => 'integer',
            'kilos_estimados' => 'decimal:2',
            'fecha_estimacion' => 'date:Y-m-d',
        ];
    }

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class);
    }
}
