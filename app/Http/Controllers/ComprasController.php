<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\Cliente;
use App\Models\Egreso;
use App\Models\Ingreso;
use App\Models\OrdenCompra;
use App\Models\ItemGasto;
use App\Models\OrdenCompraDetalle;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SolicitudCotizacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComprasController extends Controller
{
    protected function paginated($paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    protected function fmtOC(int $num): string
    {
        return 'OC-' . str_pad((string) $num, 3, '0', STR_PAD_LEFT);
    }

    protected function estAprobacion(?string $est): string
    {
        return match ($est) {
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'anulado' => 'Anulado',
            default => 'Pendiente',
        };
    }

    protected function estRecepcion(?string $est): string
    {
        return match ($est) {
            'recibido' => 'Recibido',
            'parcial' => 'Parcial',
            default => 'Pendiente',
        };
    }

    protected function formOptions(): array
    {
        return [
            'proveedores' => Proveedor::orderBy('razon_social')
                ->get(['id', 'razon_social'])
                ->map(fn($p) => ['value' => $p->id, 'label' => $p->razon_social]),
            'clientes' => Cliente::orderBy('razon_social')
                ->get(['id', 'razon_social'])
                ->map(fn($c) => ['value' => $c->id, 'label' => $c->razon_social]),
            'productos' => Producto::orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn($p) => ['value' => $p->id, 'label' => $p->nombre]),
            'centrosCosto' => CentroCosto::orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn($c) => ['value' => $c->id, 'label' => $c->nombre]),
            'itemsGasto' => ItemGasto::orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn($ig) => ['value' => $ig->id, 'label' => $ig->nombre]),
            'ordenesCompra' => OrdenCompra::orderBy('numero_oc', 'desc')
                ->with('proveedor')
                ->get()
                ->map(fn($oc) => [
                    'value' => $oc->id,
                    'label' => $this->fmtOC((int) $oc->numero_oc) . ' - ' . ($oc->proveedor?->razon_social ?? ''),
                ]),
            'metodosPago' => [
                ['value' => 'transferencia', 'label' => 'Transferencia'],
                ['value' => 'cheque', 'label' => 'Cheque'],
                ['value' => 'efectivo', 'label' => 'Efectivo'],
            ],
        ];
    }

    public function purchaseOrders(Request $request)
    {
        $query = OrdenCompra::with('proveedor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_oc', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"));
            });
        }
        if ($proveedorId = $request->get('proveedor_id')) {
            $query->where('proveedor_id', $proveedorId);
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_emision', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_emision', '<=', $dateTo);
        }
        if ($estRec = $request->get('estado_recepcion')) {
            if ($estRec === 'recibido_total') {
                $query->where('estado', 'recibido');
            } elseif ($estRec === 'recibido_parcial') {
                $query->where('estado', 'parcial');
            }
        }
        if ($estApr = $request->get('estado_aprobacion')) {
            if ($estApr === 'aprobado') {
                $query->where('estado', 'aprobado');
            } elseif ($estApr === 'rechazado') {
                $query->where('estado', 'rechazado');
            } elseif ($estApr === 'pendiente') {
                $query->whereNotIn('estado', ['recibido', 'parcial', 'aprobado', 'rechazado', 'anulado']);
            }
        }

        $pag = $query->orderBy('fecha_emision', 'desc')
            ->paginate((int) $request->get('per_page', 15))
            ->through(fn ($oc) => [
                'id' => $oc->id,
                'nroOC' => $this->fmtOC((int) $oc->numero_oc),
                'numero_oc' => $oc->numero_oc,
                'proveedor_id' => $oc->proveedor_id,
                'proveedor' => $oc->proveedor?->razon_social ?? '—',
                'fechaEmision' => $oc->fecha_emision?->format('Y-m-d') ?? '—',
                'fechaRecepcion' => $oc->fecha_entrega?->format('Y-m-d') ?? '—',
                'neto' => (float) ($oc->total_neto ?? 0),
                'iva' => (float) ($oc->iva ?? 0),
                'total' => (float) ($oc->total ?? 0),
                'moneda' => $oc->moneda ?? 'CLP',
                'estado' => $oc->estado,
                'estadoRecepcion' => $this->estRecepcion($oc->estado),
                'estadoAprobacion' => $this->estAprobacion($oc->estado),
                'detalles' => $oc->detalles->map(fn ($d) => [
                    'id' => $d->id,
                    'producto_id' => $d->producto_id,
                    'producto' => $d->producto?->nombre ?? '',
                    'cantidad' => (float) $d->cantidad,
                    'precio_unitario' => (float) $d->precio_unitario_moneda_origen,
                    'centro_costo_id' => $d->centro_costo_id,
                    'centro_costo' => $d->centroCosto?->nombre ?? '',
                ]),
            ]);

        $filters = $request->only(['search', 'proveedor_id', 'date_from', 'date_to', 'estado_recepcion', 'estado_aprobacion']);

        return Inertia::render('compras/purchase-orders', [
            'pageTitle' => 'Órdenes de Compra',
            'items' => $this->paginated($pag),
            'filterOptions' => $this->formOptions(),
            'filters' => $filters,
        ]);
    }

    public function storeOrdenCompra(Request $request)
    {
        $data = $request->validate([
            'proveedor_id' => 'required|string',
            'fecha_emision' => 'required|date',
            'fecha_entrega' => 'nullable|date',
            'moneda' => 'nullable|string',
            'total_neto' => 'nullable|numeric',
            'iva' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'detalles' => 'nullable|json',
        ]);

        $nextNum = (OrdenCompra::max('numero_oc') ?? 999) + 1;

        $oc = OrdenCompra::create([
            'proveedor_id' => $data['proveedor_id'],
            'fecha_emision' => $data['fecha_emision'],
            'fecha_entrega' => $data['fecha_entrega'],
            'moneda' => $data['moneda'] ?? 'CLP',
            'numero_oc' => $nextNum,
            'total_neto' => $data['total_neto'] ?? 0,
            'iva' => $data['iva'] ?? 0,
            'total' => $data['total'] ?? 0,
            'estado' => 'pendiente_aprobacion',
        ]);

        $detalles = json_decode($data['detalles'] ?? '[]', true);
        foreach ($detalles as $d) {
            $oc->detalles()->create([
                'producto_id' => $d['producto_id'] ?? null,
                'cantidad' => $d['cantidad'] ?? 0,
                'precio_unitario_moneda_origen' => $d['precio_unitario'] ?? 0,
                'centro_costo_id' => $d['centro_costo_id'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'OC #' . $nextNum . ' creada');
    }

    public function updateOrdenCompra(Request $request, string $id)
    {
        $oc = OrdenCompra::findOrFail($id);

        $data = $request->validate([
            'proveedor_id' => 'required|string',
            'fecha_emision' => 'required|date',
            'fecha_entrega' => 'nullable|date',
            'moneda' => 'nullable|string',
            'total_neto' => 'nullable|numeric',
            'iva' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'detalles' => 'nullable|json',
        ]);

        $oc->update([
            'proveedor_id' => $data['proveedor_id'],
            'fecha_emision' => $data['fecha_emision'],
            'fecha_entrega' => $data['fecha_entrega'],
            'moneda' => $data['moneda'] ?? 'CLP',
            'total_neto' => $data['total_neto'] ?? 0,
            'iva' => $data['iva'] ?? 0,
            'total' => $data['total'] ?? 0,
        ]);

        $oc->detalles()->delete();
        $detalles = json_decode($data['detalles'] ?? '[]', true);
        foreach ($detalles as $d) {
            $oc->detalles()->create([
                'producto_id' => $d['producto_id'] ?? null,
                'cantidad' => $d['cantidad'] ?? 0,
                'precio_unitario_moneda_origen' => $d['precio_unitario'] ?? 0,
                'centro_costo_id' => $d['centro_costo_id'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'OC actualizada');
    }

    public function destroyOrdenCompra(string $id)
    {
        $oc = OrdenCompra::findOrFail($id);
        $oc->detalles()->delete();
        $oc->delete();

        return redirect()->back()->with('success', 'OC eliminada');
    }

    public function invoices(Request $request)
    {
        $query = Egreso::with(['ordenCompra.proveedor', 'proveedor', 'centroCosto', 'itemGasto']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_documento', 'like', "%{$search}%")
                  ->orWhereHas('ordenCompra.proveedor', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"))
                  ->orWhereHas('proveedor', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"));
            });
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_registro', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_registro', '<=', $dateTo);
        }
        if ($estPago = $request->get('estado_pago')) {
            if ($estPago === 'pagados') {
                $query->where('estado_pago', 'pagado');
            } elseif ($estPago === 'pendientes') {
                $query->whereIn('estado_pago', [null, 'pendiente']);
            }
        }
        if ($estRec = $request->get('estado_recepcion')) {
            if ($estRec === 'recibido_total') {
                $query->whereHas('ordenCompra', fn ($q) => $q->where('estado', 'recibido'));
            } elseif ($estRec === 'recibido_parcial') {
                $query->whereHas('ordenCompra', fn ($q) => $q->where('estado', 'parcial'));
            }
        }

        $pag = $query->orderBy('fecha_registro', 'desc')
            ->paginate((int) $request->get('per_page', 15))
            ->through(fn ($e) => [
                'id' => $e->id,
                'orden_compra_id' => $e->orden_compra_id,
                'tipo_origen' => $e->tipo_origen ?? 'oc',
                'folio' => $e->folio_documento ?? '—',
                'proveedor' => $e->tipo_origen === 'directo'
                    ? ($e->proveedor?->razon_social ?? '—')
                    : ($e->ordenCompra?->proveedor?->razon_social ?? '—'),
                'proveedor_id' => $e->tipo_origen === 'directo'
                    ? ($e->proveedor_id ?? null)
                    : ($e->ordenCompra?->proveedor_id ?? null),
                'centro_costo_id' => $e->tipo_origen === 'directo' ? ($e->centro_costo_id ?? null) : null,
                'centro_costo' => $e->tipo_origen === 'directo' ? ($e->centroCosto?->nombre ?? '—') : null,
                'item_gasto_id' => $e->tipo_origen === 'directo' ? ($e->item_gasto_id ?? null) : null,
                'item_gasto' => $e->tipo_origen === 'directo' ? ($e->itemGasto?->nombre ?? '—') : null,
                'tipoDoc' => $e->tipo_documento ?? '—',
                'fechaRecepcion' => $e->fecha_registro?->format('Y-m-d') ?? '—',
                'neto' => round((float) ($e->monto_total_moneda_base ?? 0) / 1.19, 0),
                'iva' => round((float) ($e->monto_total_moneda_base ?? 0) - round((float) ($e->monto_total_moneda_base ?? 0) / 1.19, 0), 0),
                'total' => (float) ($e->monto_total_moneda_base ?? 0),
                'estadoRecepcion' => $e->tipo_origen === 'directo' ? '—' : $this->estRecepcion($e->ordenCompra?->estado),
                'estadoPago' => match ($e->estado_pago) {
                    'pagado' => 'Pagado',
                    'abono_parcial' => 'Parcial',
                    default => 'Pendiente',
                },
            ]);

        $filters = $request->only(['search', 'date_from', 'date_to', 'estado_pago', 'estado_recepcion']);

        return Inertia::render('compras/invoices', [
            'pageTitle' => 'Egresos y Recepciones',
            'items' => $this->paginated($pag),
            'filterOptions' => $this->formOptions(),
            'filters' => $filters,
        ]);
    }

    public function storeEgreso(Request $request)
    {
        $rules = [
            'tipo_origen' => 'required|in:oc,directo',
            'tipo_documento' => 'required|string',
            'folio_documento' => 'nullable|string',
            'fecha_registro' => 'required|date',
            'monto_total_moneda_base' => 'required|numeric',
            'estado_pago' => 'nullable|string',
        ];

        if ($request->get('tipo_origen') === 'directo') {
            $rules['proveedor_id'] = 'required|string';
            $rules['centro_costo_id'] = 'required|string';
            $rules['item_gasto_id'] = 'required|string';
        } else {
            $rules['orden_compra_id'] = 'required|string';
        }

        $data = $request->validate($rules);

        if ($data['tipo_origen'] === 'directo') {
            $data['orden_compra_id'] = null;
        } else {
            $data['proveedor_id'] = null;
            $data['centro_costo_id'] = null;
            $data['item_gasto_id'] = null;
        }

        Egreso::create($data);

        return redirect()->back()->with('success', 'Egreso creado');
    }

    public function updateEgreso(Request $request, string $id)
    {
        $egreso = Egreso::findOrFail($id);

        $rules = [
            'tipo_origen' => 'required|in:oc,directo',
            'tipo_documento' => 'required|string',
            'folio_documento' => 'nullable|string',
            'fecha_registro' => 'required|date',
            'monto_total_moneda_base' => 'required|numeric',
            'estado_pago' => 'nullable|string',
        ];

        if ($request->get('tipo_origen') === 'directo') {
            $rules['proveedor_id'] = 'required|string';
            $rules['centro_costo_id'] = 'required|string';
            $rules['item_gasto_id'] = 'required|string';
        } else {
            $rules['orden_compra_id'] = 'required|string';
        }

        $data = $request->validate($rules);

        if ($data['tipo_origen'] === 'directo') {
            $data['orden_compra_id'] = null;
        } else {
            $data['proveedor_id'] = null;
            $data['centro_costo_id'] = null;
            $data['item_gasto_id'] = null;
        }

        $egreso->update($data);

        return redirect()->back()->with('success', 'Egreso actualizado');
    }

    public function destroyEgreso(string $id)
    {
        Egreso::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Egreso eliminado');
    }

    public function payments(Request $request)
    {
        $query = Pago::with('egreso.ordenCompra.proveedor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('egreso.ordenCompra.proveedor', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"))
                  ->orWhereHas('egreso', fn ($q) => $q->where('folio_documento', 'like', "%{$search}%"));
            });
        }
        if ($metodoPago = $request->get('medio_pago')) {
            $query->where('metodo_pago', $metodoPago);
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_pago', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_pago', '<=', $dateTo);
        }
        if ($estPago = $request->get('estado_pago')) {
            if ($estPago === 'pagados') {
                $query->whereHas('egreso', fn ($q) => $q->where('estado_pago', 'pagado'));
            } elseif ($estPago === 'pendientes') {
                $query->whereHas('egreso', fn ($q) => $q->whereIn('estado_pago', [null, 'pendiente']));
            }
        }

        $pag = $query->orderBy('fecha_pago', 'desc')
            ->paginate((int) $request->get('per_page', 15))
            ->through(fn ($p) => [
                'id' => $p->id,
                'egreso_id' => $p->egreso_id,
                'fechaPago' => $p->fecha_pago?->format('Y-m-d') ?? '—',
                'proveedor' => $p->egreso?->ordenCompra?->proveedor?->razon_social ?? '—',
                'nroDocumento' => $p->egreso?->folio_documento ?? '—',
                'monto' => (float) ($p->monto_moneda_base ?? 0),
                'medioPago' => $p->metodo_pago ?? '—',
                'banco' => $p->cuenta_bancaria_origen ?? '—',
                'estado' => $p->egreso?->estado_pago === 'pagado' ? 'Pagado' : 'Pendiente',
            ]);

        $filters = $request->only(['search', 'medio_pago', 'date_from', 'date_to', 'estado_pago']);

        return Inertia::render('compras/payments', [
            'pageTitle' => 'Reporte de Pagos',
            'items' => $this->paginated($pag),
            'filterOptions' => $this->formOptions(),
            'filters' => $filters,
        ]);
    }

    public function storePago(Request $request)
    {
        $data = $request->validate([
            'egreso_id' => 'required|string',
            'fecha_pago' => 'required|date',
            'monto_moneda_base' => 'required|numeric',
            'metodo_pago' => 'nullable|string',
            'cuenta_bancaria_origen' => 'nullable|string',
        ]);

        Pago::create($data);

        return redirect()->back()->with('success', 'Pago registrado');
    }

    public function updatePago(Request $request, string $id)
    {
        $pago = Pago::findOrFail($id);

        $data = $request->validate([
            'egreso_id' => 'required|string',
            'fecha_pago' => 'required|date',
            'monto_moneda_base' => 'required|numeric',
            'metodo_pago' => 'nullable|string',
            'cuenta_bancaria_origen' => 'nullable|string',
        ]);

        $pago->update($data);

        return redirect()->back()->with('success', 'Pago actualizado');
    }

    public function destroyPago(string $id)
    {
        Pago::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Pago eliminado');
    }

    public function purchaseOrdersReport()
    {
        $ocs = OrdenCompra::with('proveedor', 'aprobadoPor')
            ->orderBy('fecha_emision', 'desc')
            ->get()
            ->map(fn($oc) => [
                'id' => $oc->id,
                'nroOC' => $this->fmtOC((int) $oc->numero_oc),
                'proveedor' => $oc->proveedor?->razon_social ?? '—',
                'fecha' => $oc->fecha_emision?->format('Y-m-d') ?? '—',
                'monto' => (float) ($oc->total ?? 0),
                'aprobadoPor' => $oc->aprobadoPor?->name ?? '—',
                'fechaAprobacion' => $oc->aprobadoPor ? $oc->updated_at?->format('Y-m-d') : '—',
                'estado' => $this->estAprobacion($oc->estado),
            ]);

        return Inertia::render('compras/purchase-orders-report', [
            'pageTitle' => 'Reporte de Aprobación OC',
            'items' => $ocs,
            'filterOptions' => $this->formOptions(),
        ]);
    }

    public function incomes(Request $request)
    {
        $query = Ingreso::with('cliente');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_documento', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"));
            });
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_emision', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_emision', '<=', $dateTo);
        }

        $pag = $query->orderBy('fecha_emision', 'desc')
            ->paginate((int) $request->get('per_page', 15))
            ->through(fn ($i) => [
                'id' => $i->id,
                'cliente_id' => $i->cliente_id,
                'folio' => $i->folio_documento ?? '—',
                'cliente' => $i->cliente?->razon_social ?? '—',
                'tipoDoc' => $i->tipo_documento ?? '—',
                'fecha' => $i->fecha_emision?->format('Y-m-d') ?? '—',
                'neto' => (float) ($i->monto_neto ?? 0),
                'iva' => (float) ($i->iva ?? 0),
                'total' => (float) ($i->monto_total ?? 0),
                'estado' => match ($i->estado) {
                    'pagado' => 'Pagado',
                    'anulado' => 'Anulado',
                    default => 'Pendiente',
                },
            ]);

        $filters = $request->only(['search', 'date_from', 'date_to']);

        return Inertia::render('compras/incomes', [
            'pageTitle' => 'Ingresos',
            'items' => $this->paginated($pag),
            'filterOptions' => $this->formOptions(),
            'filters' => $filters,
        ]);
    }

    public function storeIngreso(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|string',
            'tipo_documento' => 'required|string',
            'folio_documento' => 'nullable|string',
            'fecha_emision' => 'required|date',
            'moneda' => 'nullable|string',
            'monto_neto' => 'required|numeric',
            'iva' => 'required|numeric',
            'monto_total' => 'required|numeric',
            'estado' => 'nullable|string',
            'descripcion' => 'nullable|string',
        ]);

        Ingreso::create($data);

        return redirect()->back()->with('success', 'Ingreso creado');
    }

    public function updateIngreso(Request $request, string $id)
    {
        $ingreso = Ingreso::findOrFail($id);

        $data = $request->validate([
            'cliente_id' => 'required|string',
            'tipo_documento' => 'required|string',
            'folio_documento' => 'nullable|string',
            'fecha_emision' => 'required|date',
            'moneda' => 'nullable|string',
            'monto_neto' => 'required|numeric',
            'iva' => 'required|numeric',
            'monto_total' => 'required|numeric',
            'estado' => 'nullable|string',
            'descripcion' => 'nullable|string',
        ]);

        $ingreso->update($data);

        return redirect()->back()->with('success', 'Ingreso actualizado');
    }

    public function destroyIngreso(string $id)
    {
        Ingreso::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Ingreso eliminado');
    }

    public function cashFlowReport()
    {
        $ingresos = Ingreso::orderBy('fecha_emision')
            ->get()
            ->map(fn($i) => [
                'fecha' => $i->fecha_emision?->format('Y-m-d') ?? '—',
                'tipo' => 'Ingreso',
                'descripcion' => ($i->tipo_documento ?? 'Documento') . ' ' . ($i->folio_documento ?? ''),
                'monto' => (float) ($i->monto_total ?? 0),
            ]);

        $egresos = Egreso::orderBy('fecha_registro')
            ->get()
            ->map(fn($e) => [
                'fecha' => $e->fecha_registro?->format('Y-m-d') ?? '—',
                'tipo' => 'Egreso',
                'descripcion' => ($e->tipo_documento ?? 'Documento') . ' ' . ($e->folio_documento ?? ''),
                'monto' => -(float) ($e->monto_total_moneda_base ?? 0),
            ]);

        $movimientos = collect([...$ingresos, ...$egresos])
            ->sortBy('fecha')
            ->values();

        $acumulado = 0;
        $movimientos = $movimientos->map(function ($m) use (&$acumulado) {
            $acumulado += (float) $m['monto'];
            $m['saldoAcumulado'] = round($acumulado, 0);
            return $m;
        });

        return Inertia::render('compras/cash-flow-report', [
            'pageTitle' => 'Flujo de Caja',
            'items' => $movimientos,
            'filterOptions' => $this->formOptions(),
        ]);
    }

    public function quotations(Request $request)
    {
        $query = SolicitudCotizacion::with('proveedor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_solicitud', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"));
            });
        }
        if ($proveedorId = $request->get('proveedor_id')) {
            $query->where('proveedor_id', $proveedorId);
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_solicitud', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_solicitud', '<=', $dateTo);
        }
        if ($estado = $request->get('estado_aprobacion')) {
            if ($estado === 'aprobado') {
                $query->where('estado', 'aprobado');
            } elseif ($estado === 'rechazado') {
                $query->where('estado', 'rechazado');
            } elseif ($estado === 'pendiente') {
                $query->whereNotIn('estado', ['aprobado', 'rechazado', 'anulado']);
            }
        }

        $pag = $query->orderBy('fecha_solicitud', 'desc')
            ->paginate((int) $request->get('per_page', 15))
            ->through(fn ($s) => [
                'id' => $s->id,
                'proveedor_id' => $s->proveedor_id,
                'nroSolicitud' => $s->numero_solicitud ?? '—',
                'proveedor' => $s->proveedor?->razon_social ?? '—',
                'fecha' => $s->fecha_solicitud?->format('Y-m-d') ?? '—',
                'producto' => $s->descripcion ?? '—',
                'montoEstimado' => (float) ($s->monto_estimado ?? 0),
                'estado' => $this->estAprobacion($s->estado),
            ]);

        $filters = $request->only(['search', 'proveedor_id', 'date_from', 'date_to', 'estado_aprobacion']);

        return Inertia::render('compras/quotations', [
            'pageTitle' => 'Solicitudes de Cotización',
            'items' => $this->paginated($pag),
            'filterOptions' => $this->formOptions(),
            'filters' => $filters,
        ]);
    }

    public function storeCotizacion(Request $request)
    {
        $data = $request->validate([
            'proveedor_id' => 'required|string',
            'numero_solicitud' => 'nullable|string',
            'fecha_solicitud' => 'required|date',
            'descripcion' => 'nullable|string',
            'monto_estimado' => 'required|numeric',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        SolicitudCotizacion::create($data);

        return redirect()->back()->with('success', 'Cotización creada');
    }

    public function updateCotizacion(Request $request, string $id)
    {
        $cot = SolicitudCotizacion::findOrFail($id);

        $data = $request->validate([
            'proveedor_id' => 'required|string',
            'numero_solicitud' => 'nullable|string',
            'fecha_solicitud' => 'required|date',
            'descripcion' => 'nullable|string',
            'monto_estimado' => 'required|numeric',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $cot->update($data);

        return redirect()->back()->with('success', 'Cotización actualizada');
    }

    public function destroyCotizacion(string $id)
    {
        SolicitudCotizacion::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Cotización eliminada');
    }
}
