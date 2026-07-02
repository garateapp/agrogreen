<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSafetyCheck extends Model
{
    use HasUuids, Auditable;

    protected $table = 'application_safety_checks';

    protected $fillable = [
        'application_record_id',
        'epp_guantes',
        'epp_mascarilla',
        'epp_overol',
        'epp_botas',
        'epp_proteccion_ocular',
        'senalizacion_instalada',
        'agua_emergencia',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'epp_guantes' => 'boolean',
            'epp_mascarilla' => 'boolean',
            'epp_overol' => 'boolean',
            'epp_botas' => 'boolean',
            'epp_proteccion_ocular' => 'boolean',
            'senalizacion_instalada' => 'boolean',
            'agua_emergencia' => 'boolean',
        ];
    }

    public function applicationRecord(): BelongsTo
    {
        return $this->belongsTo(ApplicationRecord::class, 'application_record_id');
    }
}
