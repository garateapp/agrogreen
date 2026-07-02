<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\RecalculateCPPJob;
use App\Models\InventarioMovimiento;
use App\Models\InventarioMovimientoDetalle;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InventarioService
{
    /**
     * Registra un movimiento de inventario y dispara la recalculación
     * cronológica del Costo Promedio Ponderado (CPP) en segundo plano.
     *
     * @param  array{tenant_id: string, bodega_origen_id?: string|null, bodega_destino_id?: string|null, tipo_movimiento: string, documento_referencia_id?: string|null, fecha_movimiento: Carbon|string}  $movimientoData
     * @param  array{producto_id: string, cantidad: float|int, costo_unitario_moneda_base: float|int}  $detalleData
     */
    public function registrarMovimientoYRecalcularCPP(array $movimientoData, array $detalleData): InventarioMovimiento
    {
        $movimiento = InventarioMovimiento::create([
            'tenant_id' => $movimientoData['tenant_id'],
            'bodega_origen_id' => $movimientoData['bodega_origen_id'] ?? null,
            'bodega_destino_id' => $movimientoData['bodega_destino_id'] ?? null,
            'tipo_movimiento' => $movimientoData['tipo_movimiento'],
            'documento_referencia_id' => $movimientoData['documento_referencia_id'] ?? null,
            'fecha_movimiento' => $movimientoData['fecha_movimiento'],
        ]);

        $ultimoDetalle = InventarioMovimientoDetalle::whereHas('movimiento', function ($q) use ($detalleData, $movimiento) {
            $q->where('tenant_id', $movimiento->tenant_id);
        })
            ->where('producto_id', $detalleData['producto_id'])
            ->where('movimiento_id', '!=', $movimiento->id)
            ->orderByDesc(
                InventarioMovimiento::select('fecha_movimiento')
                    ->whereColumn('id', 'inventario_movimiento_detalles.movimiento_id')
            )
            ->first();

        $saldoAnterior = (float) ($ultimoDetalle?->saldo_stock_posterior ?? 0);

        $cantidad = (float) $detalleData['cantidad'];

        $saldoPosterior = in_array($movimientoData['tipo_movimiento'], ['consumo_faena', 'ajuste_inventario'], true)
            ? $saldoAnterior - abs($cantidad)
            : $saldoAnterior + abs($cantidad);

        $detalle = InventarioMovimientoDetalle::create([
            'movimiento_id' => $movimiento->id,
            'producto_id' => $detalleData['producto_id'],
            'cantidad' => $cantidad,
            'costo_unitario_moneda_base' => $detalleData['costo_unitario_moneda_base'],
            'saldo_stock_anterior' => $saldoAnterior,
            'saldo_stock_posterior' => max($saldoPosterior, 0),
        ]);

        RecalculateCPPJob::dispatch(
            productoId: $detalleData['producto_id'],
            tenantId: $movimientoData['tenant_id'],
            desdeFecha: $movimientoData['fecha_movimiento'],
        );

        return $movimiento;
    }
}
