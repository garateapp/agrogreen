<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

// These are used in aprobar() / anular() methods for inventory integration
// Inventory movements are created to track stock deduction/restitution

class ApplicationRecord extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = [
        'tenant_id',
        'codigo',
        'cuartel_id',
        'variedad_id',
        'temporada',
        'superficie',
        'fecha_aplicacion',
        'hora_inicio',
        'hora_termino',
        'estado',
        'objetivo_tipo',
        'objetivo_nombre',
        'responsable_id',
        'aplicador_id',
        'supervisor_id',
        'equipo_id',
        'observaciones',
        'creado_por',
        'aprobado_por',
        'aprobado_en',
        'anulado_por',
        'motivo_anulacion',
    ];

    protected function casts(): array
    {
        return [
            'superficie' => 'decimal:2',
            'fecha_aplicacion' => 'date',
            'hora_inicio' => 'string',
            'hora_termino' => 'string',
            'aprobado_en' => 'datetime',
        ];
    }

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class, 'cuartel_id');
    }

    public function variedad(): BelongsTo
    {
        return $this->belongsTo(Variedad::class, 'variedad_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function aplicadorRel(): BelongsTo
    {
        return $this->belongsTo(Aplicador::class, 'aplicador_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoAplicacion::class, 'equipo_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(ApplicationRecordProducto::class, 'application_record_id');
    }

    public function clima(): HasOne
    {
        return $this->hasOne(ApplicationWeatherCondition::class, 'application_record_id');
    }

    public function seguridad(): HasOne
    {
        return $this->hasOne(ApplicationSafetyCheck::class, 'application_record_id');
    }

    public function envases(): HasMany
    {
        return $this->hasMany(ApplicationContainerDisposal::class, 'application_record_id');
    }

    public function aprobar(string $userId): void
    {
        DB::transaction(function () use ($userId) {
            $this->update([
                'estado' => 'aprobada',
                'aprobado_por' => $userId,
                'aprobado_en' => now(),
            ]);

            $movimiento = InventarioMovimiento::create([
                'tenant_id' => $this->tenant_id,
                'bodega_origen_id' => null,
                'bodega_destino_id' => null,
                'tipo_movimiento' => 'consumo_faena',
                'documento_referencia_id' => $this->id,
                'fecha_movimiento' => now(),
            ]);

            foreach ($this->productos as $producto) {
                $lote = $producto->lote;
                $nuevoStock = $lote ? max(0, $lote->cantidad_disponible - $producto->cantidad_total) : 0;
                $anterior = $lote?->cantidad_disponible ?? 0;

                InventarioMovimientoDetalle::create([
                    'movimiento_id' => $movimiento->id,
                    'producto_id' => $producto->productoSAG->producto_id,
                    'lote_id' => $producto->lote_id,
                    'cantidad' => -$producto->cantidad_total,
                    'costo_unitario_moneda_base' => 0,
                    'saldo_stock_anterior' => $anterior,
                    'saldo_stock_posterior' => $nuevoStock,
                ]);

                if ($lote) {
                    $lote->decrement('cantidad_disponible', $producto->cantidad_total);
                }
            }
        });
    }

    public function anular(int $userId, string $motivo): void
    {
        DB::transaction(function () use ($userId, $motivo) {
            $estadoAnterior = $this->estado;

            $this->update([
                'estado' => 'anulada',
                'anulado_por' => $userId,
                'motivo_anulacion' => $motivo,
            ]);

            if ($estadoAnterior === 'aprobada') {
                $movimiento = InventarioMovimiento::create([
                    'tenant_id' => $this->tenant_id,
                    'bodega_origen_id' => null,
                    'bodega_destino_id' => null,
                    'tipo_movimiento' => 'ajuste_inventario',
                    'documento_referencia_id' => $this->id,
                    'fecha_movimiento' => now(),
                ]);

                foreach ($this->productos as $producto) {
                    $lote = $producto->lote;

                    InventarioMovimientoDetalle::create([
                        'movimiento_id' => $movimiento->id,
                        'producto_id' => $producto->productoSAG->producto_id,
                        'lote_id' => $producto->lote_id,
                        'cantidad' => $producto->cantidad_total,
                        'costo_unitario_moneda_base' => 0,
                        'saldo_stock_anterior' => $lote?->cantidad_disponible ?? 0,
                        'saldo_stock_posterior' => $lote ? ($lote->cantidad_disponible + $producto->cantidad_total) : 0,
                    ]);

                    if ($lote) {
                        $lote->increment('cantidad_disponible', $producto->cantidad_total);
                    }
                }
            }
        });
    }
}
