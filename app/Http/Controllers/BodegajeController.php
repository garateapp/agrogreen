<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\CentroCosto;
use App\Models\GoodsReceipt;
use App\Models\InventarioMovimiento;
use App\Models\InventarioMovimientoDetalle;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BodegajeController extends Controller
{
    public function goodsReceipts()
    {
        return Inertia::render('bodegaje/goods-receipts', [
            'pageTitle' => 'Guías de Entrada',
            'items' => GoodsReceipt::with('proveedor')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'numero' => $r->numero,
                    'fecha' => $r->fecha_emision->format('Y-m-d'),
                    'proveedor' => $r->proveedor?->razon_social,
                    'tipo' => $r->tipo,
                    'total' => collect($r->lineas ?? [])->sum('subtotal'),
                    'lineas' => $r->lineas,
                    'descripcion' => $r->descripcion,
                    'proveedor_id' => $r->proveedor_id,
                    'distribuir_costos' => $r->distribuir_costos,
                    'descuento_linea' => $r->descuento_linea,
                    'vencimiento_lote' => $r->vencimiento_lote,
                ]),
            'proveedores' => Proveedor::orderBy('razon_social')->get(['id', 'razon_social', 'rut']),
            'productos' => Producto::orderBy('nombre')->get(['id', 'nombre']),
            'bodegas' => Bodega::orderBy('nombre')->get(['id', 'nombre']),
            'centroCostos' => CentroCosto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function storeGoodsReceipt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:255',
            'fecha_emision' => 'required|date',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:productos,servicios',
            'proveedor_id' => 'nullable|uuid',
            'distribuir_costos' => 'nullable|boolean',
            'descuento_linea' => 'nullable|boolean',
            'vencimiento_lote' => 'nullable|boolean',
            'lineas' => 'nullable|json',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id'] = $tenantId;
        $lineas = json_decode($request->input('lineas', '[]'), true);
        $validated['lineas'] = $lineas;

        $receipt = GoodsReceipt::create($validated);

        // Create inventory movement + lotes for each product line
        if (is_array($lineas) && count($lineas) > 0) {
            $movimiento = InventarioMovimiento::create([
                'tenant_id' => $tenantId,
                'codigo' => 'GR-'.$receipt->numero,
                'bodega_origen_id' => null,
                'bodega_destino_id' => $lineas[0]['bodega_id'] ?? null,
                'tipo_movimiento' => 'entrada_compra',
                'documento_referencia_id' => $receipt->id,
                'fecha_movimiento' => $validated['fecha_emision'].' 00:00:00',
            ]);

            foreach ($lineas as $linea) {
                $cantidad = (float) ($linea['cantidad'] ?? 0);
                $costoUnitario = (float) ($linea['precio'] ?? 0);
                $productoId = $linea['producto_id'] ?? null;
                $bodegaId = $linea['bodega_id'] ?? null;

                if (! $productoId || $cantidad <= 0) {
                    continue;
                }

                // Create or find lote
                $codigoLote = 'L-'.$receipt->numero.'-'.strtoupper(substr((string) $productoId, 0, 6));
                $lote = Lote::create([
                    'tenant_id' => $tenantId,
                    'bodega_id' => $bodegaId,
                    'producto_id' => $productoId,
                    'codigo_lote' => $codigoLote,
                    'fecha_vencimiento' => null,
                    'cantidad_inicial' => $cantidad,
                    'cantidad_disponible' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                ]);

                // Create movimiento detalle
                InventarioMovimientoDetalle::create([
                    'movimiento_id' => $movimiento->id,
                    'producto_id' => $productoId,
                    'lote_id' => $lote->id,
                    'cantidad' => $cantidad,
                    'costo_unitario_moneda_base' => $costoUnitario,
                    'saldo_stock_anterior' => 0,
                    'saldo_stock_posterior' => $cantidad,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Guía de entrada creada correctamente.');
    }

    public function updateGoodsReceipt(string $id, Request $request): RedirectResponse
    {
        $receipt = GoodsReceipt::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|string|max:255',
            'fecha_emision' => 'required|date',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:productos,servicios',
            'proveedor_id' => 'nullable|uuid',
            'distribuir_costos' => 'nullable|boolean',
            'descuento_linea' => 'nullable|boolean',
            'vencimiento_lote' => 'nullable|boolean',
            'lineas' => 'nullable|json',
            'motivo' => 'required|string|min:10',
        ]);

        $validated['lineas'] = json_decode($request->input('lineas', '[]'), true);
        $validated['descripcion'] = ($validated['descripcion'] ?? '')
            ."\n[Editado ".now()->format('Y-m-d H:i').'] '.$validated['motivo'];
        $receipt->update($validated);

        return redirect()->back()->with('success', 'Guía de entrada actualizada correctamente.');
    }

    public function destroyGoodsReceipt(string $id, Request $request): RedirectResponse
    {
        $validated = $request->validate(['motivo' => 'required|string|min:10']);
        GoodsReceipt::findOrFail($id)->update([
            'descripcion' => ($request->descripcion ?? '')
                ."\n[Eliminado ".now()->format('Y-m-d H:i').'] '.$validated['motivo'],
        ]);
        GoodsReceipt::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Guía de entrada eliminada correctamente.');
    }

    public function goodsIssues()
    {
        $productos = Producto::with('unidadMedida:id,nombre')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'unidad' => $p->unidadMedida?->nombre ?? '',
                'stockTotal' => (float) $p->lotes()->disponible()->sum('cantidad_disponible'),
                'stockPorBodega' => $p->lotes()
                    ->disponible()
                    ->selectRaw('bodega_id, SUM(cantidad_disponible) as stock')
                    ->groupBy('bodega_id')
                    ->get()
                    ->mapWithKeys(fn ($l) => [$l->bodega_id => (float) $l->stock])
                    ->toArray(),
            ]);

        return Inertia::render('bodegaje/goods-issues', [
            'pageTitle' => 'Guías de Consumo',
            'items' => InventarioMovimiento::where('tipo_movimiento', 'consumo_faena')
                ->with('detalles.producto.unidadMedida')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'codigo' => $m->codigo,
                    'fecha' => $m->fecha_movimiento->format('Y-m-d'),
                    'bodega' => $m->bodegaOrigen?->nombre,
                    'bodega_origen_id' => $m->bodega_origen_id,
                    'descripcion' => $m->descripcion,
                    'total' => $m->detalles->sum(fn ($d) => abs((float) $d->cantidad)),
                    'lineas' => $m->detalles->map(fn ($d) => [
                        'producto_id' => $d->producto_id,
                        'producto' => $d->producto?->nombre ?? 'N/A',
                        'unidad' => $d->producto?->unidadMedida?->nombre ?? '',
                        'cantidad' => abs((float) $d->cantidad),
                    ]),
                ]),
            'productos' => $productos,
            'bodegas' => Bodega::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function storeGoodsIssue(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fecha_emision' => 'required|date',
            'descripcion' => 'nullable|string',
            'bodega_origen_id' => 'required|uuid|exists:bodegas,id',
            'lineas' => 'required|array|min:1',
            'lineas.*.producto_id' => 'required|uuid|exists:productos,id',
            'lineas.*.cantidad' => 'required|numeric|min:0.0001',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $ultimo = InventarioMovimiento::where('tenant_id', $tenantId)
            ->where('tipo_movimiento', 'consumo_faena')
            ->whereYear('created_at', now()->year)
            ->max('codigo');

        $secuencial = $ultimo ? (int) str($ultimo)->afterLast('-') + 1 : 1;
        $codigo = 'GC-'.now()->year.'-'.str_pad((string) $secuencial, 4, '0', STR_PAD_LEFT);

        $movimiento = InventarioMovimiento::create([
            'tenant_id' => $tenantId,
            'codigo' => $codigo,
            'bodega_origen_id' => $validated['bodega_origen_id'],
            'bodega_destino_id' => null,
            'tipo_movimiento' => 'consumo_faena',
            'documento_referencia_id' => null,
            'fecha_movimiento' => $validated['fecha_emision'].' 00:00:00',
            'descripcion' => $validated['descripcion'] ?? '',
        ]);

        foreach ($validated['lineas'] as $linea) {
            $cantidad = (float) $linea['cantidad'];
            $productoId = $linea['producto_id'];

            $totalDisponible = (float) Lote::where('producto_id', $productoId)->disponible()->sum('cantidad_disponible');

            if ($totalDisponible < $cantidad) {
                return redirect()->back()->withErrors("Stock insuficiente para el producto seleccionado. Disponible: $totalDisponible");
            }

            $lotes = Lote::where('producto_id', $productoId)->disponible()->orderBy('fecha_vencimiento')->orderBy('created_at')->get();

            $restante = $cantidad;
            foreach ($lotes as $lote) {
                if ($restante <= 0) {
                    break;
                }
                $deducir = min($restante, (float) $lote->cantidad_disponible);
                $saldoAnterior = (float) $lote->cantidad_disponible;
                $lote->decrement('cantidad_disponible', $deducir);

                InventarioMovimientoDetalle::create([
                    'movimiento_id' => $movimiento->id,
                    'producto_id' => $productoId,
                    'lote_id' => $lote->id,
                    'cantidad' => -$deducir,
                    'costo_unitario_moneda_base' => 0,
                    'saldo_stock_anterior' => $saldoAnterior,
                    'saldo_stock_posterior' => $saldoAnterior - $deducir,
                ]);

                $restante -= $deducir;
            }
        }

        return redirect()->back()->with('success', 'Guía de consumo creada correctamente.');
    }

    public function updateGoodsIssue(string $id, Request $request): RedirectResponse
    {
        $movimiento = InventarioMovimiento::with('detalles')->findOrFail($id);

        $validated = $request->validate([
            'fecha_emision' => 'required|date',
            'descripcion' => 'nullable|string',
            'bodega_origen_id' => 'required|uuid|exists:bodegas,id',
            'lineas' => 'required|array|min:1',
            'lineas.*.producto_id' => 'required|uuid|exists:productos,id',
            'lineas.*.cantidad' => 'required|numeric|min:0.0001',
            'motivo' => 'required|string|min:10',
        ]);

        $descripcion = ($validated['descripcion'] ?? '')
            ."\n[Editado ".now()->format('Y-m-d H:i').'] '.$validated['motivo'];

        try {
            DB::transaction(function () use ($movimiento, $validated, $descripcion) {
                // Reverse old stock deductions
                foreach ($movimiento->detalles as $detalle) {
                    $lote = Lote::find($detalle->lote_id);
                    if ($lote) {
                        $lote->increment('cantidad_disponible', abs((float) $detalle->cantidad));
                    }
                    $detalle->delete();
                }

                // Re-apply new stock deductions (FIFO)
                foreach ($validated['lineas'] as $linea) {
                    $cantidad = (float) $linea['cantidad'];
                    $productoId = $linea['producto_id'];

                    $totalDisponible = (float) Lote::where('producto_id', $productoId)->disponible()->sum('cantidad_disponible');

                    if ($totalDisponible < $cantidad) {
                        throw new \Exception("Stock insuficiente para el producto seleccionado. Disponible: $totalDisponible");
                    }

                    $lotes = Lote::where('producto_id', $productoId)->disponible()->orderBy('fecha_vencimiento')->orderBy('created_at')->get();

                    $restante = $cantidad;
                    foreach ($lotes as $lote) {
                        if ($restante <= 0) {
                            break;
                        }
                        $deducir = min($restante, (float) $lote->cantidad_disponible);
                        $saldoAnterior = (float) $lote->cantidad_disponible;
                        $lote->decrement('cantidad_disponible', $deducir);

                        InventarioMovimientoDetalle::create([
                            'movimiento_id' => $movimiento->id,
                            'producto_id' => $productoId,
                            'lote_id' => $lote->id,
                            'cantidad' => -$deducir,
                            'costo_unitario_moneda_base' => 0,
                            'saldo_stock_anterior' => $saldoAnterior,
                            'saldo_stock_posterior' => $saldoAnterior - $deducir,
                        ]);

                        $restante -= $deducir;
                    }
                }

                $movimiento->update([
                    'bodega_origen_id' => $validated['bodega_origen_id'],
                    'fecha_movimiento' => $validated['fecha_emision'].' 00:00:00',
                    'descripcion' => $descripcion,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Guía de consumo actualizada correctamente.');
    }

    public function destroyGoodsIssue(string $id, Request $request): RedirectResponse
    {
        $validated = $request->validate(['motivo' => 'required|string|min:10']);

        $movimiento = InventarioMovimiento::with('detalles')->findOrFail($id);

        DB::transaction(function () use ($movimiento, $validated) {
            // Reverse stock deductions
            foreach ($movimiento->detalles as $detalle) {
                $lote = Lote::find($detalle->lote_id);
                if ($lote) {
                    $lote->increment('cantidad_disponible', abs((float) $detalle->cantidad));
                }
                $detalle->delete();
            }

            $movimiento->update([
                'descripcion' => ($movimiento->descripcion ?? '')
                    ."\n[Eliminado ".now()->format('Y-m-d H:i').'] '.$validated['motivo'],
            ]);

            $movimiento->delete();
        });

        return redirect()->back()->with('success', 'Guía de consumo eliminada correctamente.');
    }

    public function inventoryReport()
    {
        $bodegaMovimientoExpression = 'COALESCE(lotes.bodega_id, inventario_movimientos.bodega_destino_id, inventario_movimientos.bodega_origen_id)';

        $movimientos = InventarioMovimientoDetalle::query()
            ->join('inventario_movimientos', 'inventario_movimiento_detalles.movimiento_id', '=', 'inventario_movimientos.id')
            ->leftJoin('lotes', 'inventario_movimiento_detalles.lote_id', '=', 'lotes.id')
            ->selectRaw('inventario_movimiento_detalles.producto_id')
            ->selectRaw($bodegaMovimientoExpression.' as bodega_id')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN inventario_movimientos.tipo_movimiento = 'entrada_compra'
                            AND inventario_movimiento_detalles.cantidad > 0
                        THEN inventario_movimiento_detalles.cantidad
                        ELSE 0
                    END
                ) as entradas
            ")
            ->selectRaw("
                SUM(
                    CASE
                        WHEN inventario_movimientos.tipo_movimiento IN ('consumo_faena', 'ajuste_inventario')
                            AND inventario_movimiento_detalles.cantidad < 0
                        THEN ABS(inventario_movimiento_detalles.cantidad)
                        ELSE 0
                    END
                ) as salidas
            ")
            ->groupBy('inventario_movimiento_detalles.producto_id')
            ->groupByRaw($bodegaMovimientoExpression)
            ->get()
            ->keyBy(fn ($movimiento) => $movimiento->producto_id.'-'.$movimiento->bodega_id);

        $items = Lote::selectRaw('producto_id, bodega_id, SUM(cantidad_disponible) as stock')
            ->selectRaw('SUM(cantidad_inicial) as inicial')
            ->selectRaw('SUM(cantidad_disponible * costo_unitario) as subtotal')
            ->selectRaw('
                CASE
                    WHEN SUM(cantidad_disponible) > 0
                    THEN SUM(cantidad_disponible * costo_unitario) / SUM(cantidad_disponible)
                    ELSE 0
                END as valor_unitario
            ')
            ->where('cantidad_disponible', '>', 0)
            ->groupBy('producto_id', 'bodega_id')
            ->with('producto.unidadMedida', 'bodega')
            ->get()
            ->map(function ($lote) use ($movimientos) {
                $prod = $lote->producto;
                $movementKey = $lote->producto_id.'-'.$lote->bodega_id;
                $movimiento = $movimientos->get($movementKey);

                return [
                    'id' => $movementKey,
                    'producto' => $prod?->nombre ?? 'N/A',
                    'unidad' => $prod?->unidadMedida?->nombre ?? '',
                    'bodega' => $lote->bodega?->nombre ?? 'General',
                    'bodega_id' => $lote->bodega_id,
                    'inicial' => (float) $lote->inicial,
                    'entradas' => (float) ($movimiento?->entradas ?? 0),
                    'salidas' => (float) ($movimiento?->salidas ?? 0),
                    'stock' => (float) $lote->stock,
                    'valorUnitario' => round((float) $lote->valor_unitario, 0),
                    'subtotal' => round((float) $lote->subtotal, 0),
                ];
            })->values();

        return Inertia::render('bodegaje/inventory-report', [
            'pageTitle' => 'Reporte de Inventario',
            'items' => $items,
            'bodegas' => Bodega::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function warehouseTransfers()
    {
        $productos = Producto::with('unidadMedida:id,nombre')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'unidad' => $p->unidadMedida?->nombre ?? '',
                'stockPorBodega' => $p->lotes()
                    ->disponible()
                    ->selectRaw('bodega_id, SUM(cantidad_disponible) as stock')
                    ->groupBy('bodega_id')
                    ->get()
                    ->mapWithKeys(fn ($l) => [$l->bodega_id => (float) $l->stock])
                    ->toArray(),
            ]);

        $items = InventarioMovimiento::where('tipo_movimiento', 'traspaso')
            ->with('bodegaOrigen:id,nombre', 'bodegaDestino:id,nombre')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'folio' => $m->codigo,
                'origen' => $m->bodegaOrigen?->nombre,
                'origen_id' => $m->bodega_origen_id,
                'destino' => $m->bodegaDestino?->nombre,
                'destino_id' => $m->bodega_destino_id,
                'fecha' => $m->fecha_movimiento->format('Y-m-d'),
            ]);

        return Inertia::render('bodegaje/warehouse-transfers', [
            'pageTitle' => 'Traspaso entre Bodegas',
            'items' => $items,
            'bodegas' => Bodega::orderBy('nombre')->get(['id', 'nombre']),
            'productos' => $productos,
        ]);
    }

    public function storeWarehouseTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bodega_origen_id' => 'required|uuid|exists:bodegas,id|different:bodega_destino_id',
            'bodega_destino_id' => 'required|uuid|exists:bodegas,id|different:bodega_origen_id',
            'fecha_emision' => 'required|date',
            'descripcion' => 'nullable|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.producto_id' => 'required|uuid|exists:productos,id',
            'lineas.*.cantidad' => 'required|numeric|min:0.0001',
        ]);

        $tenantId = auth()->user()->tenant_id;

        try {
            DB::transaction(function () use ($tenantId, $validated) {
                $year = now()->year;
                $ultimo = InventarioMovimiento::where('tenant_id', $tenantId)
                    ->where('tipo_movimiento', 'traspaso')
                    ->where('codigo', 'like', "TR-$year-%")
                    ->orderByDesc('codigo')
                    ->lockForUpdate()
                    ->value('codigo');

                $secuencial = $ultimo ? (int) ((string) str($ultimo)->afterLast('-')) + 1 : 1;
                $codigo = 'TR-'.$year.'-'.str_pad((string) $secuencial, 4, '0', STR_PAD_LEFT);

                $movimiento = InventarioMovimiento::create([
                    'tenant_id' => $tenantId,
                    'codigo' => $codigo,
                    'bodega_origen_id' => $validated['bodega_origen_id'],
                    'bodega_destino_id' => $validated['bodega_destino_id'],
                    'tipo_movimiento' => 'traspaso',
                    'fecha_movimiento' => $validated['fecha_emision'].' 00:00:00',
                    'descripcion' => $validated['descripcion'] ?? '',
                ]);

                foreach ($validated['lineas'] as $linea) {
                    $cantidad = (float) $linea['cantidad'];
                    $productoId = $linea['producto_id'];

                    $totalDisponible = (float) Lote::where('producto_id', $productoId)
                        ->where('bodega_id', $validated['bodega_origen_id'])
                        ->disponible()
                        ->sum('cantidad_disponible');

                    if ($totalDisponible < $cantidad) {
                        throw new \RuntimeException(
                            "Stock insuficiente para el producto en bodega origen. Disponible: $totalDisponible"
                        );
                    }

                    $lotes = Lote::where('producto_id', $productoId)
                        ->where('bodega_id', $validated['bodega_origen_id'])
                        ->disponible()
                        ->orderBy('fecha_vencimiento')
                        ->orderBy('created_at')
                        ->get();

                    $restante = $cantidad;
                    foreach ($lotes as $lote) {
                        if ($restante <= 0) {
                            break;
                        }

                        $deducir = min($restante, (float) $lote->cantidad_disponible);
                        $saldoAnterior = (float) $lote->cantidad_disponible;
                        $costoUnitario = (float) $lote->costo_unitario;

                        $lote->decrement('cantidad_disponible', $deducir);

                        InventarioMovimientoDetalle::create([
                            'movimiento_id' => $movimiento->id,
                            'producto_id' => $productoId,
                            'lote_id' => $lote->id,
                            'cantidad' => -$deducir,
                            'costo_unitario_moneda_base' => $costoUnitario,
                            'saldo_stock_anterior' => $saldoAnterior,
                            'saldo_stock_posterior' => $saldoAnterior - $deducir,
                        ]);

                        $destino = Lote::create([
                            'tenant_id' => $tenantId,
                            'bodega_id' => $validated['bodega_destino_id'],
                            'producto_id' => $productoId,
                            'codigo_lote' => $lote->codigo_lote.'-'.$codigo,
                            'fecha_vencimiento' => $lote->fecha_vencimiento,
                            'cantidad_inicial' => $deducir,
                            'cantidad_disponible' => $deducir,
                            'costo_unitario' => $costoUnitario,
                        ]);

                        InventarioMovimientoDetalle::create([
                            'movimiento_id' => $movimiento->id,
                            'producto_id' => $productoId,
                            'lote_id' => $destino->id,
                            'cantidad' => $deducir,
                            'costo_unitario_moneda_base' => $costoUnitario,
                            'saldo_stock_anterior' => 0,
                            'saldo_stock_posterior' => $deducir,
                        ]);

                        $restante -= $deducir;
                    }
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Traspaso creado correctamente.');
    }

    public function productConsumptionReport()
    {
        $items = InventarioMovimientoDetalle::select('producto_id')
            ->selectRaw('SUM(cantidad) as total_cantidad')
            ->selectRaw('AVG(costo_unitario_moneda_base) as precio_promedio')
            ->whereHas('movimiento', fn ($q) => $q->where('tipo_movimiento', 'consumo_faena'))
            ->groupBy('producto_id')
            ->having('total_cantidad', '<', 0)
            ->with('producto.unidadMedida')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->producto_id,
                'producto' => $d->producto?->nombre ?? 'N/A',
                'unidad' => $d->producto?->unidadMedida?->nombre ?? '',
                'cantidad' => abs((float) $d->total_cantidad),
                'precioUnitario' => round(abs((float) $d->precio_promedio), 0),
                'total' => round(abs((float) $d->total_cantidad) * abs((float) $d->precio_promedio), 0),
            ])->values();

        return Inertia::render('bodegaje/product-consumption-report', [
            'pageTitle' => 'Reporte de Consumo de Productos',
            'items' => $items,
        ]);
    }
}
