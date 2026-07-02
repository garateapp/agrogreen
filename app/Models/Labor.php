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

class Labor extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'labores';

    protected $fillable = [
        'tenant_id',
        'plantilla_id',
        'actividad_id',
        'centro_costo_id',
        'supervisor_id',
        'estado',
        'fecha_programada',
        'fecha_ejecucion',
        'fecha_fin_estimada',
        'observaciones',
        'avance',
        'valor_trato_unitario',
        'requiere_empleados',
        'es_ciclica',
        'frecuencia',
        'fecha_fin_ciclo',
        'inicio_real',
        'fin_real',
    ];

    protected function casts(): array
    {
        return [
            'fecha_programada' => 'date',
            'fecha_ejecucion' => 'date',
            'fecha_fin_estimada' => 'date',
            'avance' => 'integer',
            'requiere_empleados' => 'boolean',
            'es_ciclica' => 'boolean',
            'fecha_fin_ciclo' => 'date',
            'inicio_real' => 'datetime',
            'fin_real' => 'datetime',
            'valor_trato_unitario' => 'decimal:2',
        ];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(self::class, 'plantilla_id');
    }

    public function instancias(): HasMany
    {
        return $this->hasMany(self::class, 'plantilla_id');
    }

    public function cuarteles(): BelongsToMany
    {
        return $this->belongsToMany(Cuartel::class, 'labor_cuarteles')
            ->withTimestamps();
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(LaborEmpleado::class, 'labor_id');
    }
}
