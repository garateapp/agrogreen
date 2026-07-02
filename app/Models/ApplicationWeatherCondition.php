<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationWeatherCondition extends Model
{
    use HasUuids, Auditable;

    protected $table = 'application_weather_conditions';

    protected $fillable = [
        'application_record_id',
        'temperatura',
        'humedad',
        'viento_velocidad',
        'viento_direccion',
        'estado_general',
        'riesgo_deriva',
        'fuente',
    ];

    protected function casts(): array
    {
        return [
            'temperatura' => 'decimal:1',
            'humedad' => 'decimal:1',
            'viento_velocidad' => 'decimal:1',
        ];
    }

    public function applicationRecord(): BelongsTo
    {
        return $this->belongsTo(ApplicationRecord::class, 'application_record_id');
    }
}
