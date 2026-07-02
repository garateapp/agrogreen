<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ContenedorCosecha;
use App\Models\Cuartel;
use App\Models\Estimacion;
use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Presupuesto::with('detalles', 'temporada');

        if ($anho = $request->get('anho_fiscal')) {
            $query->where('anho_fiscal', $anho);
        }
        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        $presupuestos = $query->orderBy('anho_fiscal', 'desc')
            ->orderBy('mes', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'temporada_id' => $p->temporada_id,
                'temporada_nombre' => $p->temporada?->nombre,
                'anho_fiscal' => $p->anho_fiscal,
                'mes' => $p->mes,
                'estado' => $p->estado,
                'tipo_cambio_usd' => $p->tipo_cambio_usd ? (float) $p->tipo_cambio_usd : null,
                'total_lineas' => $p->detalles->count(),
                'monto_total' => (float) $p->detalles->sum('valor_total'),
                'created_at' => $p->created_at?->format('Y-m-d'),
            ]);

        $dashboard = null;
        $selectedId = $request->get('selected_id');
        if ($selectedId) {
            $presupuesto = Presupuesto::with('detalles.cuartel.centroCosto', 'detalles.cuartel.especie', 'detalles.actividad')
                ->find($selectedId);
            if ($presupuesto) {
                $dashboard = $this->buildDashboard($presupuesto->detalles);
                $dashboard['presupuesto'] = [
                    'id' => $presupuesto->id,
                    'anho_fiscal' => $presupuesto->anho_fiscal,
                    'mes' => $presupuesto->mes,
                    'estado' => $presupuesto->estado,
                    'total_valor' => (float) $presupuesto->detalles->sum('valor_total'),
                    'total_jh' => (float) $presupuesto->detalles->sum('jh_totales'),
                    'total_lineas' => $presupuesto->detalles->count(),
                ];
            }
        }

        return Inertia::render('presupuesto/index', [
            'pageTitle' => 'Presupuestos',
            'presupuestos' => $presupuestos,
            'filters' => $request->only(['anho_fiscal', 'estado']),
            'dashboard' => $dashboard,
            'selectedId' => $selectedId,
        ]);
    }

    public function create()
    {
        $actividades = Actividad::orderBy('nombre')
            ->get(['id', 'nombre', 'icono', 'color', 'tipo_labor', 'unidad_medida_id', 'valor']);

        $cuarteles = Cuartel::with('centroCosto', 'especie', 'variedades')
            ->orderBy('nombre')
            ->get();

        $hasTrato = Actividad::where('tipo_labor', 'trato')->exists();

        $grupos = [];
        foreach ($cuarteles as $c) {
            $agrupador = $c->centroCosto?->agrupador ?? 'Sin agrupador';
            if (!isset($grupos[$agrupador])) {
                $grupos[$agrupador] = [
                    'agrupador' => $agrupador,
                    'total_cuarteles' => 0,
                    'total_hectareas' => 0,
                    'total_plantas' => 0,
                    'especies' => collect(),
                    'tiene_trato' => $hasTrato,
                ];
            }
            $grupos[$agrupador]['total_cuarteles']++;
            $grupos[$agrupador]['total_hectareas'] += (float) $c->superficie_hectareas;
            $grupos[$agrupador]['total_plantas'] += (int) $c->variedades->sum(fn ($v) => $v->pivot?->cantidad_plantas ?? 0);
            $grupos[$agrupador]['especies']->push($c->especie?->nombre ?? '—');
        }

        foreach ($grupos as &$g) {
            $g['especies'] = $g['especies']->unique()->values()->toArray();
        }

        $estimaciones = Estimacion::where('estado', 'confirmado')
            ->orderBy('anho', 'desc')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($e) => [
                'value' => $e->id,
                'label' => sprintf('%s — %s (%.1f kg)', $e->cuartel?->nombre ?? '?', $e->nombre, $e->kilos_estimados),
                'cuartel_id' => $e->cuartel_id,
                'kilos_estimados' => (float) $e->kilos_estimados,
            ]);

        $contenedoresCosecha = ContenedorCosecha::orderBy('nombre')
            ->get(['id', 'nombre', 'peso_bin_kg'])
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => sprintf('%s (%.1f kg/bin)', $c->nombre, $c->peso_bin_kg),
                'peso_bin_kg' => (float) $c->peso_bin_kg,
            ]);

        $temporadas = Temporada::orderBy('nombre')
            ->get(['id', 'nombre', 'fecha_inicio', 'fecha_fin'])
            ->map(fn ($t) => ['value' => $t->id, 'label' => $t->nombre]);

        return Inertia::render('presupuesto/form', [
            'pageTitle' => 'Nuevo Presupuesto',
            'presupuesto' => null,
            'grupos' => array_values($grupos),
            'total_cuarteles' => $cuarteles->count(),
            'total_actividades' => $actividades->count(),
            'total_lineas' => $cuarteles->count() * $actividades->count(),
            'estimaciones' => $estimaciones->values(),
            'contenedoresCosecha' => $contenedoresCosecha->values(),
            'temporadas' => $temporadas->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'anho_fiscal' => 'nullable|integer|min:2020|max:2099',
            'mes' => 'nullable|integer|min:1|max:12',
            'temporada_id' => 'nullable|string|exists:temporadas,id',
            'tipo_cambio_usd' => 'nullable|numeric|min:0',
            'grupos' => 'nullable|array',
            'grupos.*.agrupador' => 'required|string',
            'grupos.*.rendimiento_promedio' => 'nullable|numeric|min:0',
            'grupos.*.valor_unitario' => 'nullable|numeric|min:0',
            'grupos.*.contenedor_cosecha_id' => 'nullable|string',
            'grupos.*.kilos_estimados' => 'nullable|numeric|min:0',
        ]);

        $presupuesto = Presupuesto::create([
            'tenant_id' => auth()->user()->tenant_id,
            'temporada_id' => $data['temporada_id'] ?? null,
            'anho_fiscal' => $data['anho_fiscal'],
            'mes' => $data['mes'],
            'estado' => 'borrador',
            'tipo_cambio_usd' => $data['tipo_cambio_usd'] ?? null,
        ]);

        if (empty($data['grupos'])) {
            return redirect()->route('presupuesto.edit', $presupuesto->id)
                ->with('success', 'Presupuesto creado (sin líneas). Completa los grupos para generar las líneas.');
        }

        $cuarteles = Cuartel::with('centroCosto', 'variedades')->get();
        $actividades = Actividad::where('prespuestable',1)->get();
        $contenedores = ContenedorCosecha::pluck('peso_bin_kg', 'id');
        $gruposMap = collect($data['grupos'])->keyBy('agrupador');

        $detalles = [];
        $now = now();

        foreach ($cuarteles as $cuartel) {
            $agrupador = $cuartel->centroCosto?->agrupador ?? 'Sin agrupador';
            $grupo = $gruposMap->get($agrupador);
            if (!$grupo) {
                continue;
            }

            $rendimiento = (float) ($grupo['rendimiento_promedio'] ?? 0);
            $valorUnitario = (float) ($grupo['valor_unitario'] ?? 0);
            $contenedorId = $grupo['contenedor_cosecha_id'] ?? null;
            $kilosEstimados = (float) ($grupo['kilos_estimados'] ?? 0);
            $kgPorContenedor = $contenedorId ? ($contenedores[$contenedorId] ?? 1) : 1;

            $hectareas = (float) $cuartel->superficie_hectareas;
            $nPlantas = (int) $cuartel->variedades->sum(fn ($v) => $v->pivot?->cantidad_plantas ?? 0);

            foreach ($actividades as $actividad) {
                if ($actividad->tipo_labor === 'trato') {
                    $totalContenedores = $kgPorContenedor > 0 ? $kilosEstimados / $kgPorContenedor : 0;
                    $jh = $rendimiento > 0 ? $totalContenedores / $rendimiento : 0;
                } else {
                    $jh = $rendimiento > 0 ? $hectareas / $rendimiento : 0;
                }
                $valorTotal = $jh * $valorUnitario;

                $detalles[] = [
                    'id' => (string) Str::uuid(),
                    'presupuesto_id' => $presupuesto->id,
                    'anho_fiscal' => $presupuesto->anho_fiscal,
                    'mes' => $presupuesto->mes,
                    'cuartel_id' => $cuartel->id,
                    'actividad_id' => $actividad->id,
                    'estimacion_id' => null,
                    'contenedor_cosecha_id' => $contenedorId,
                    'rendimiento_promedio' => $rendimiento,
                    'hectareas' => $hectareas,
                    'n_plantas' => $nPlantas,
                    'kilos_estimados' => $actividad->tipo_labor === 'trato' ? $kilosEstimados : null,
                    'jh_totales' => $jh,
                    'valor_unitario' => $valorUnitario,
                    'valor_total' => $valorTotal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($detalles, 500) as $chunk) {
            PresupuestoDetalle::insert($chunk);
        }

        return redirect()->route('presupuesto.edit', $presupuesto->id)
            ->with('success', sprintf('Presupuesto creado con %d líneas', count($detalles)));
    }

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

    protected function buildDashboard($detalles): array
    {
        $gastosPorActividad = $detalles->groupBy('actividad_id')->map(function ($rows) {
            $act = $rows->first()->actividad;
            return [
                'actividad_id' => $rows->first()->actividad_id,
                'actividad_nombre' => $act?->nombre ?? '—',
                'icono' => $act?->icono ?? 'Agriculture',
                'color' => $act?->color ?? '#4CAF50',
                'tipo_labor' => $act?->tipo_labor ?? 'dia',
                'total_jh' => round((float) $rows->sum('jh_totales'), 2),
                'total_valor' => round((float) $rows->sum('valor_total'), 2),
                'lineas' => $rows->count(),
            ];
        })->sortByDesc('total_valor')->values();

        $gastosPorAgrupador = $detalles->groupBy(fn ($d) => $d->cuartel?->centroCosto?->agrupador ?? 'Sin agrupador')
            ->map(function ($rows) {
                return [
                    'agrupador' => $rows->first()->cuartel?->centroCosto?->agrupador ?? 'Sin agrupador',
                    'lineas' => $rows->count(),
                    'total_jh' => round((float) $rows->sum('jh_totales'), 2),
                    'total_valor' => round((float) $rows->sum('valor_total'), 2),
                    'porcentaje' => 0,
                ];
            })->values();

        $maxValor = $gastosPorAgrupador->max('total_valor') ?: 1;
        $gastosPorAgrupador = $gastosPorAgrupador->map(fn ($g) => [
            ...$g,
            'porcentaje' => round($g['total_valor'] / $maxValor * 100, 1),
        ]);

        $cuarteles = Cuartel::with('centroCosto', 'especie', 'variedades')->orderBy('nombre')->get();
        $hasTrato = Actividad::where('tipo_labor', 'trato')->exists();

        $grupos = [];
        foreach ($cuarteles->groupBy(fn ($c) => $c->centroCosto?->agrupador ?? 'Sin agrupador') as $agrupador => $items) {
            $rows = $detalles->filter(fn ($d) => ($d->cuartel?->centroCosto?->agrupador ?? 'Sin agrupador') === $agrupador);

            $grupos[] = [
                'agrupador' => $agrupador,
                'total_cuarteles' => $items->count(),
                'total_hectareas' => round((float) $items->sum('superficie_hectareas'), 2),
                'tiene_trato' => $hasTrato,
                'especies' => $items->pluck('especie.nombre')->unique()->filter()->values()->toArray(),
                'total_lineas' => $rows->count(),
                'total_jh' => round((float) $rows->sum('jh_totales'), 2),
                'total_valor' => round((float) $rows->sum('valor_total'), 2),
                'rendimiento_promedio' => $rows->count() > 0 ? round((float) $rows->avg('rendimiento_promedio'), 2) : 0,
                'valor_unitario_promedio' => $rows->count() > 0 ? round((float) $rows->avg('valor_unitario'), 2) : 0,
                'contenedor_cosecha_id' => $rows->pluck('contenedor_cosecha_id')->filter()->unique()->values()->first(),
            ];
        }

        return [
            'gastosPorActividad' => $gastosPorActividad,
            'gastosPorAgrupador' => $gastosPorAgrupador,
            'grupos' => $grupos,
        ];
    }

    public function edit(Request $request, string $id)
    {
        $presupuesto = Presupuesto::with('detalles.cuartel.centroCosto', 'detalles.cuartel.especie', 'detalles.actividad')
            ->findOrFail($id);

        $detalles = $presupuesto->detalles;

        $cuarteles = Cuartel::with('centroCosto', 'especie', 'variedades')
            ->orderBy('nombre')
            ->get();

        $hasTrato = Actividad::where('tipo_labor', 'trato')->exists();

        $gruposResumen = [];
        foreach ($cuarteles->groupBy(fn ($c) => $c->centroCosto?->agrupador ?? 'Sin agrupador') as $agrupador => $items) {
            $rows = $detalles->filter(fn ($d) => ($d->cuartel?->centroCosto?->agrupador ?? 'Sin agrupador') === $agrupador);

            $gruposResumen[] = [
                'agrupador' => $agrupador,
                'total_cuarteles' => $items->count(),
                'total_hectareas' => round((float) $items->sum('superficie_hectareas'), 2),
                'tiene_trato' => $hasTrato,
                'especies' => $items->pluck('especie.nombre')->unique()->filter()->values()->toArray(),
                'total_lineas' => $rows->count(),
                'total_jh' => round((float) $rows->sum('jh_totales'), 2),
                'total_valor' => round((float) $rows->sum('valor_total'), 2),
                'rendimiento_promedio' => $rows->count() > 0 ? round((float) $rows->avg('rendimiento_promedio'), 2) : 0,
                'valor_unitario_promedio' => $rows->count() > 0 ? round((float) $rows->avg('valor_unitario'), 2) : 0,
                'contenedor_cosecha_id' => $rows->pluck('contenedor_cosecha_id')->filter()->unique()->values()->first(),
            ];
        }

        $contenedoresCosecha = ContenedorCosecha::orderBy('nombre')
            ->get(['id', 'nombre', 'peso_bin_kg'])
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => sprintf('%s (%.1f kg/bin)', $c->nombre, $c->peso_bin_kg),
                'peso_bin_kg' => (float) $c->peso_bin_kg,
            ]);

        $presupuestoData = [
            'id' => $presupuesto->id,
            'temporada_id' => $presupuesto->temporada_id,
            'anho_fiscal' => $presupuesto->anho_fiscal,
            'mes' => $presupuesto->mes,
            'estado' => $presupuesto->estado,
            'tipo_cambio_usd' => $presupuesto->tipo_cambio_usd ? (float) $presupuesto->tipo_cambio_usd : null,
            'total_valor' => (float) $detalles->sum('valor_total'),
            'total_jh' => (float) $detalles->sum('jh_totales'),
            'total_lineas' => $detalles->count(),
        ];

        // --- Paginated detail rows with filters ---
        $detallesQuery = $presupuesto->detalles()->with('cuartel.centroCosto', 'cuartel.especie', 'actividad');

        if ($agrupador = $request->get('agrupador')) {
            $detallesQuery->whereHas('cuartel.centroCosto', fn ($q) => $q->where('agrupador', $agrupador));
        }
        if ($actividadId = $request->get('actividad_id')) {
            $detallesQuery->where('actividad_id', $actividadId);
        }
        if ($cuartelId = $request->get('cuartel_id')) {
            $detallesQuery->where('cuartel_id', $cuartelId);
        }
        if ($tipoLabor = $request->get('tipo_labor')) {
            $detallesQuery->whereHas('actividad', fn ($q) => $q->where('tipo_labor', $tipoLabor));
        }
        if ($anhoFiscal = $request->get('anho_fiscal')) {
            $detallesQuery->where('anho_fiscal', $anhoFiscal);
        }
        if ($mesFilter = $request->get('mes')) {
            $detallesQuery->where('mes', $mesFilter);
        }

        $perPage = min((int) $request->get('per_page', 50), 200);
        $detallesPag = $detallesQuery->orderBy('cuartel_id')->orderBy('actividad_id')
            ->paginate($perPage)
            ->through(fn ($d) => [
                'id' => $d->id,
                'anho_fiscal' => $d->anho_fiscal,
                'mes' => $d->mes,
                'cuartel_id' => $d->cuartel_id,
                'cuartel_nombre' => $d->cuartel?->nombre ?? '—',
                'especie' => $d->cuartel?->especie?->nombre ?? '—',
                'agrupador' => $d->cuartel?->centroCosto?->agrupador ?? '—',
                'centro_costo' => $d->cuartel?->centroCosto?->codigo ?? '—',
                'actividad_id' => $d->actividad_id,
                'actividad_nombre' => $d->actividad?->nombre ?? '—',
                'tipo_labor' => $d->actividad?->tipo_labor ?? 'dia',
                'contenedor_cosecha_id' => $d->contenedor_cosecha_id,
                'rendimiento_promedio' => (float) $d->rendimiento_promedio,
                'hectareas' => $d->hectareas ? (float) $d->hectareas : null,
                'n_plantas' => $d->n_plantas,
                'kilos_estimados' => $d->kilos_estimados ? (float) $d->kilos_estimados : null,
                'jh_totales' => (float) $d->jh_totales,
                'valor_unitario' => (float) $d->valor_unitario,
                'valor_total' => (float) $d->valor_total,
            ]);

        $filterAgrupadores = $detalles->pluck('cuartel.centroCosto.agrupador')
            ->unique()->filter()->sort()->values()->map(fn ($a) => ['value' => $a, 'label' => $a]);

        $filterActividades = Actividad::orderBy('nombre')
            ->get(['id', 'nombre', 'tipo_labor'])
            ->map(fn ($a) => ['value' => $a->id, 'label' => $a->nombre, 'tipo_labor' => $a->tipo_labor]);

        $filterCuarteles = Cuartel::whereIn('id', $detalles->pluck('cuartel_id')->unique())
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn ($c) => ['value' => $c->id, 'label' => $c->nombre]);

        $MESES = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $filterAnhos = $detalles->pluck('anho_fiscal')->unique()->filter()->sort()->values()
            ->map(fn ($a) => ['value' => (string) $a, 'label' => (string) $a]);

        $filterMeses = $detalles->pluck('mes')->unique()->filter()->sort()->values()
            ->map(fn ($m) => ['value' => (string) $m, 'label' => $MESES[(int) $m] ?? "Mes {$m}"]);

        return Inertia::render('presupuesto/form', [
            'pageTitle' => 'Editar Presupuesto',
            'presupuesto' => $presupuestoData,
            'grupos' => $gruposResumen,
            'detallesPaginated' => $this->paginated($detallesPag),
            'filterOptions' => [
                'agrupadores' => $filterAgrupadores,
                'actividades' => $filterActividades,
                'cuarteles' => $filterCuarteles,
                'anhos' => $filterAnhos,
                'meses' => $filterMeses,
            ],
            'detalleFilters' => $request->only(['agrupador', 'actividad_id', 'cuartel_id', 'tipo_labor', 'anho_fiscal', 'mes', 'page', 'per_page']),
            'total_cuarteles' => $cuarteles->count(),
            'total_actividades' => Actividad::count(),
            'total_lineas' => $cuarteles->count() * Actividad::count(),
            'estimaciones' => [],
            'contenedoresCosecha' => $contenedoresCosecha->values(),
            'temporadas' => Temporada::orderBy('nombre')
                ->get(['id', 'nombre', 'fecha_inicio', 'fecha_fin'])
                ->map(fn ($t) => ['value' => $t->id, 'label' => $t->nombre]),
        ]);
    }

    // ─── Temporadas CRUD ───

    public function temporadas()
    {
        $temporadas = Temporada::orderBy('nombre')
            ->get(['id', 'nombre', 'fecha_inicio', 'fecha_fin']);

        return Inertia::render('presupuesto/index', [
            'pageTitle' => 'Temporadas',
            'presupuestos' => [],
            'filters' => [],
            'dashboard' => null,
            'selectedId' => null,
            'temporadas' => $temporadas,
            'autoOpenTemporadas' => true,
        ]);
    }

    public function storeTemporada(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        Temporada::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$data,
        ]);

        return redirect()->back()->with('success', 'Temporada creada');
    }

    public function updateTemporada(Request $request, string $id)
    {
        $temporada = Temporada::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $temporada->update($data);

        return redirect()->back()->with('success', 'Temporada actualizada');
    }

    public function destroyTemporada(string $id)
    {
        Temporada::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Temporada eliminada');
    }

    // ─── Clone Presupuesto ───

    public function clonePresupuesto(string $id)
    {
        $presupuesto = Presupuesto::with('detalles')->findOrFail($id);

        $newPresupuesto = $presupuesto->replicate(['id']);
        $newPresupuesto->id = (string) Str::uuid();
        $newPresupuesto->estado = 'borrador';
        $newPresupuesto->save();

        foreach ($presupuesto->detalles as $detalle) {
            $newDetalle = $detalle->replicate(['id', 'presupuesto_id']);
            $newDetalle->id = (string) Str::uuid();
            $newDetalle->presupuesto_id = $newPresupuesto->id;
            $newDetalle->save();
        }

        return redirect()->route('presupuesto.edit', $newPresupuesto->id)
            ->with('success', sprintf('Presupuesto clonado con %d líneas', $presupuesto->detalles->count()));
    }

    // ─── Update ───

    public function update(Request $request, string $id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        $data = $request->validate([
            'anho_fiscal' => 'nullable|integer|min:2020|max:2099',
            'mes' => 'nullable|integer|min:1|max:12',
            'temporada_id' => 'nullable|string|exists:temporadas,id',
            'estado' => 'nullable|string|in:borrador,aprobado,cerrado',
            'tipo_cambio_usd' => 'nullable|numeric|min:0',
            'grupos' => 'nullable|array',
            'grupos.*.agrupador' => 'required|string',
            'grupos.*.rendimiento_promedio' => 'nullable|numeric|min:0',
            'grupos.*.valor_unitario' => 'nullable|numeric|min:0',
            'grupos.*.contenedor_cosecha_id' => 'nullable|string',
            'grupos.*.kilos_estimados' => 'nullable|numeric|min:0',
        ]);

        $presupuesto->update([
            'temporada_id' => $data['temporada_id'] ?? $presupuesto->temporada_id,
            'anho_fiscal' => $data['anho_fiscal'],
            'mes' => $data['mes'],
            'estado' => $data['estado'] ?? $presupuesto->estado,
            'tipo_cambio_usd' => $data['tipo_cambio_usd'] ?? null,
        ]);

        if (isset($data['grupos']) && $data['grupos']) {
            $presupuesto->detalles()->delete();

            $cuarteles = Cuartel::with('centroCosto', 'variedades')->get();
            $actividades = Actividad::all();
            $contenedores = ContenedorCosecha::pluck('peso_bin_kg', 'id');
            $gruposMap = collect($data['grupos'])->keyBy('agrupador');

            $detalles = [];
            $now = now();

            foreach ($cuarteles as $cuartel) {
                $agrupador = $cuartel->centroCosto?->agrupador ?? 'Sin agrupador';
                $grupo = $gruposMap->get($agrupador);
                if (!$grupo) {
                    continue;
                }

                $rendimiento = (float) ($grupo['rendimiento_promedio'] ?? 0);
                $valorUnitario = (float) ($grupo['valor_unitario'] ?? 0);
                $contenedorId = $grupo['contenedor_cosecha_id'] ?? null;
                $kilosEstimados = (float) ($grupo['kilos_estimados'] ?? 0);
                $kgPorContenedor = $contenedorId ? ($contenedores[$contenedorId] ?? 1) : 1;

                $hectareas = (float) $cuartel->superficie_hectareas;
                $nPlantas = (int) $cuartel->variedades->sum(fn ($v) => $v->pivot?->cantidad_plantas ?? 0);

                foreach ($actividades as $actividad) {
                    if ($actividad->tipo_labor === 'trato') {
                        $totalContenedores = $kgPorContenedor > 0 ? $kilosEstimados / $kgPorContenedor : 0;
                        $jh = $rendimiento > 0 ? $totalContenedores / $rendimiento : 0;
                    } else {
                        $jh = $rendimiento > 0 ? $hectareas / $rendimiento : 0;
                    }
                    $valorTotal = $jh * $valorUnitario;

                    $detalles[] = [
                        'id' => (string) Str::uuid(),
                        'presupuesto_id' => $presupuesto->id,
                        'anho_fiscal' => $presupuesto->anho_fiscal,
                        'mes' => $presupuesto->mes,
                        'cuartel_id' => $cuartel->id,
                        'actividad_id' => $actividad->id,
                        'estimacion_id' => null,
                        'contenedor_cosecha_id' => $contenedorId,
                        'rendimiento_promedio' => $rendimiento,
                        'hectareas' => $hectareas,
                        'n_plantas' => $nPlantas,
                        'kilos_estimados' => $actividad->tipo_labor === 'trato' ? $kilosEstimados : null,
                        'jh_totales' => $jh,
                        'valor_unitario' => $valorUnitario,
                        'valor_total' => $valorTotal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach (array_chunk($detalles, 500) as $chunk) {
                PresupuestoDetalle::insert($chunk);
            }

            return redirect()->route('presupuesto.edit', $presupuesto->id)
                ->with('success', sprintf('Presupuesto regenerado con %d líneas', count($detalles)));
        }

        return redirect()->route('presupuesto.edit', $presupuesto->id)
            ->with('success', 'Presupuesto actualizado');
    }

    protected function calculateJH(Cuartel $cuartel, Actividad $actividad, float $rendimiento, float $valorUnitario, ?string $contenedorId, float $kilosEstimados): array
    {
        $hectareas = (float) $cuartel->superficie_hectareas;

        $kgPorContenedor = 1;
        if ($contenedorId) {
            $cont = ContenedorCosecha::find($contenedorId);
            $kgPorContenedor = $cont ? (float) $cont->peso_bin_kg : 1;
        }

        if ($actividad->tipo_labor === 'trato') {
            $totalContenedores = $kgPorContenedor > 0 ? $kilosEstimados / $kgPorContenedor : 0;
            $jh = $rendimiento > 0 ? $totalContenedores / $rendimiento : 0;
        } else {
            $jh = $rendimiento > 0 ? $hectareas / $rendimiento : 0;
        }

        $valorTotal = $jh * $valorUnitario;

        return ['jh' => $jh, 'valor_total' => $valorTotal];
    }

    public function updateDetalle(Request $request, string $presupuestoId, string $detalleId)
    {
        $presupuesto = Presupuesto::findOrFail($presupuestoId);
        $detalle = PresupuestoDetalle::with('cuartel', 'actividad')
            ->where('presupuesto_id', $presupuestoId)
            ->findOrFail($detalleId);

        $data = $request->validate([
            'rendimiento_promedio' => 'nullable|numeric|min:0',
            'valor_unitario' => 'nullable|numeric|min:0',
            'contenedor_cosecha_id' => 'nullable|string',
            'kilos_estimados' => 'nullable|numeric|min:0',
            'anho_fiscal' => 'nullable|integer|min:2020|max:2099',
            'mes' => 'nullable|integer|min:1|max:12',
        ]);

        $rendimiento = (float) ($data['rendimiento_promedio'] ?? $detalle->rendimiento_promedio);
        $valorUnitario = (float) ($data['valor_unitario'] ?? $detalle->valor_unitario);
        $contenedorId = $data['contenedor_cosecha_id'] ?? $detalle->contenedor_cosecha_id;
        $kilosEstimados = (float) ($data['kilos_estimados'] ?? $detalle->kilos_estimados ?? 0);

        $calc = $this->calculateJH($detalle->cuartel, $detalle->actividad, $rendimiento, $valorUnitario, $contenedorId, $kilosEstimados);

        $updateData = [
            'rendimiento_promedio' => $rendimiento,
            'valor_unitario' => $valorUnitario,
            'contenedor_cosecha_id' => $contenedorId,
            'kilos_estimados' => $detalle->actividad->tipo_labor === 'trato' ? $kilosEstimados : null,
            'jh_totales' => $calc['jh'],
            'valor_total' => $calc['valor_total'],
            'anho_fiscal' => array_key_exists('anho_fiscal', $data) ? $data['anho_fiscal'] : $detalle->anho_fiscal,
            'mes' => array_key_exists('mes', $data) ? $data['mes'] : $detalle->mes,
        ];

        $detalle->update($updateData);

        return redirect()->back()->with('success', 'Línea actualizada');
    }

    public function cloneDetalle(string $presupuestoId, string $detalleId)
    {
        $presupuesto = Presupuesto::findOrFail($presupuestoId);
        $detalle = PresupuestoDetalle::where('presupuesto_id', $presupuestoId)->findOrFail($detalleId);

        $clone = $detalle->replicate(['id']);
        $clone->id = (string) Str::uuid();
        $clone->save();

        return redirect()->back()->with('success', 'Línea clonada');
    }

    public function copyDetalleToAgrupador(string $presupuestoId, string $detalleId)
    {
        $presupuesto = Presupuesto::findOrFail($presupuestoId);
        $detalle = PresupuestoDetalle::with('cuartel.centroCosto', 'actividad')
            ->where('presupuesto_id', $presupuestoId)
            ->findOrFail($detalleId);

        $agrupador = $detalle->cuartel?->centroCosto?->agrupador;
        if (!$agrupador) {
            return redirect()->back()->with('error', 'El cuartel no tiene agrupador asignado');
        }

        $targetCuarteles = Cuartel::whereHas('centroCosto', fn ($q) => $q->where('agrupador', $agrupador))
            ->where('id', '!=', $detalle->cuartel_id)
            ->pluck('id');

        if ($targetCuarteles->isEmpty()) {
            return redirect()->back()->with('warning', 'No hay otros cuarteles en este agrupador');
        }

        $rendimiento = (float) $detalle->rendimiento_promedio;
        $valorUnitario = (float) $detalle->valor_unitario;
        $contenedorId = $detalle->contenedor_cosecha_id;
        $kilosEstimados = (float) ($detalle->kilos_estimados ?? 0);
        $tipoLabor = $detalle->actividad->tipo_labor;

        $updated = 0;
        foreach ($targetCuarteles as $cuartelId) {
            $cuartel = Cuartel::find($cuartelId);
            if (!$cuartel) {
                continue;
            }

            $calc = $this->calculateJH($cuartel, $detalle->actividad, $rendimiento, $valorUnitario, $contenedorId, $kilosEstimados);

            $affected = PresupuestoDetalle::where('presupuesto_id', $presupuestoId)
                ->where('cuartel_id', $cuartelId)
                ->where('actividad_id', $detalle->actividad_id)
                ->update([
                    'rendimiento_promedio' => $rendimiento,
                    'valor_unitario' => $valorUnitario,
                    'contenedor_cosecha_id' => $contenedorId,
                    'kilos_estimados' => $tipoLabor === 'trato' ? $kilosEstimados : null,
                    'jh_totales' => $calc['jh'],
                    'valor_total' => $calc['valor_total'],
                ]);

            $updated += $affected;
        }

        return redirect()->back()->with('success', "Valores copiados a {$updated} línea(s) en el agrupador '{$agrupador}'");
    }

    public function destroy(string $id)
    {
        Presupuesto::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Presupuesto eliminado');
    }
}
