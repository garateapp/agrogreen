<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodoFiscal extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'periodos_fiscales';

    protected $fillable = [
        'tenant_id',
        'ano',
        'mes',
        'cerrado',
        'cerrado_por_user_id',
        'fecha_cierre',
    ];

    protected function casts(): array
    {
        return [
            'cerrado' => 'boolean',
            'fecha_cierre' => 'datetime',
        ];
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por_user_id');
    }
}
