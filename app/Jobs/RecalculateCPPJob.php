<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\InventarioMovimiento;
use App\Models\InventarioMovimientoDetalle;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class RecalculateCPPJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * @param  string  $productoId
     * @param  string  $tenantId
     * @param  Carbon|string  $desdeFecha
     */
    public function __construct(
        private readonly string $productoId,
        private readonly string $tenantId,
        private readonly Carbon|string $desdeFecha,
    ) {}

    public function handle(): void
    {
        $fechaInicio = $this->desdeFecha instanceof Carbon
            ? $this->desdeFecha
            : Carbon::parse($this->desdeFecha);

        $detalles = InventarioMovimientoDetalle::where('producto_id', $this->productoId)
            ->whereHas('movimiento', function ($q) {
                $q->where('tenant_id', $this->tenantId)
                    ->where('fecha_movimiento', '>=', $fechaInicio);
            })
            ->join('inventario_movimientos', 'inventario_movimiento_detalles.movimiento_id', '=', 'inventario_movimientos.id')
            ->orderBy('inventario_movimientos.fecha_movimiento')
            ->orderBy('inventario_movimientos.created_at')
            ->select('inventario_movimiento_detalles.*', 'inventario_movimientos.fecha_movimiento')
            ->get();

        if ($detalles->isEmpty()) {
            return;
        }

        $primerDetalle = InventarioMovimientoDetalle::where('id', '<', $detalles->first()->id)
            ->where('producto_id', $this->productoId)
            ->whereHas('movimiento', function ($q) {
                $q->where('tenant_id', $this->tenantId);
            })
            ->orderByDesc(
                InventarioMovimiento::select('fecha_movimiento')
                    ->whereColumn('id', 'inventario_movimiento_detalles.movimiento_id')
            )
            ->first();

        $stockAcumulado = (float) ($primerDetalle?->saldo_stock_posterior ?? 0);
        $costoAcumulado = 0.0;
        $cantidadAcumulada = 0.0;

        foreach ($detalles as $detalle) {
            $cantidad = (float) $detalle->cantidad;
            $costoUnitario = (float) $detalle->costo_unitario_moneda_base;

            $saldoAnterior = $stockAcumulado;

            $movimiento = $detalle->movimiento;
            $esSalida = in_array($movimiento->tipo_movimiento, ['consumo_faena', 'ajuste_inventario'], true)
                && $cantidad < 0;

            if ($esSalida) {
                $stockAcumulado -= abs($cantidad);
            } else {
                $costoAcumulado += abs($cantidad) * $costoUnitario;
                $cantidadAcumulada += abs($cantidad);
                $stockAcumulado += abs($cantidad);
            }

            $cpp = $cantidadAcumulada > 0
                ? round($costoAcumulado / $cantidadAcumulada, 4)
                : 0.0;

            $detalle->updateQuietly([
                'saldo_stock_anterior' => max($saldoAnterior, 0),
                'saldo_stock_posterior' => max($stockAcumulado, 0),
                'costo_unitario_moneda_base' => $cpp,
            ]);
        }
    }
}
