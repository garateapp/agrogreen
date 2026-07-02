<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Tarjeta extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'tarjetas';

    protected $fillable = [
        'tenant_id',
        'codigo_qr',
        'sigla',
        'empleado_id',
        'fecha_asignacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(TarjetaAsignacion::class);
    }

    public function asignacionActiva(): ?TarjetaAsignacion
    {
        return $this->asignaciones()
            ->whereNull('fecha_desasignacion')
            ->latest('fecha_asignacion')
            ->first();
    }

    public function assignTo(Empleado $empleado, ?string $userId = null): void
    {
        DB::transaction(function () use ($empleado, $userId) {
            $now = now();

            // Deactivate other active cards for this employee
            static::where('empleado_id', $empleado->id)
                ->where('id', '!=', $this->id)
                ->where('activo', true)
                ->update(['activo' => false, 'fecha_asignacion' => null]);

            // Close previous assignment for this card if exists
            $this->asignaciones()
                ->whereNull('fecha_desasignacion')
                ->update(['fecha_desasignacion' => $now, 'desasignado_por' => $userId]);

            // Assign
            $this->empleado_id = $empleado->id;
            $this->fecha_asignacion = $now;
            $this->activo = true;
            $this->save();

            // Create history record
            $this->asignaciones()->create([
                'empleado_id' => $empleado->id,
                'fecha_asignacion' => $now,
                'asignado_por' => $userId,
            ]);
        });
    }

    public function unassign(?string $userId = null): void
    {
        DB::transaction(function () use ($userId) {
            $now = now();

            $this->asignaciones()
                ->whereNull('fecha_desasignacion')
                ->update(['fecha_desasignacion' => $now, 'desasignado_por' => $userId]);

            $this->empleado_id = null;
            $this->fecha_asignacion = null;
            $this->save();
        });
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $tarjeta) {
            if (! $tarjeta->codigo_qr && $tarjeta->sigla) {
                $prefix = $tarjeta->sigla;
                $max = static::where('codigo_qr', 'like', $prefix.'-%')
                    ->max(DB::raw("CAST(SUBSTRING(codigo_qr, LENGTH('{$prefix}') + 2) AS UNSIGNED)"));
                $tarjeta->codigo_qr = $prefix.'-'.str_pad((string)((int) $max + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
