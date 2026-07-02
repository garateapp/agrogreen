<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenAplicacionCuartel extends Model
{
    use HasUuids, SoftDeletes, Auditable;

    protected $table = 'orden_aplicacion_cuarteles';

    protected $fillable = [
        'orden_aplicacion_id',
        'cuartel_id',
    ];

    public function ordenAplicacion(): BelongsTo
    {
        return $this->belongsTo(OrdenAplicacion::class, 'orden_aplicacion_id');
    }

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class);
    }
}
