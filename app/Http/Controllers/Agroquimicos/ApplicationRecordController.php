<?php

declare(strict_types=1);

namespace App\Http\Controllers\Agroquimicos;

use App\Http\Controllers\Controller;
use App\Models\Aplicador;
use App\Models\ApplicationRecord;
use App\Models\Cuartel;
use App\Models\EquipoAplicacion;
use App\Models\Lote;
use App\Models\ProductoSAG;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApplicationRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = ApplicationRecord::with([
            'cuartel', 'variedad', 'responsable', 'aplicadorRel', 'equipo', 'productos.productoSAG',
        ])->where('tenant_id', Auth::user()->tenant_id);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('cuartel_id')) {
            $query->where('cuartel_id', $request->cuartel_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_aplicacion', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_aplicacion', '<=', $request->hasta);
        }

        return Inertia::render('agroquimicos/index', [
            'records' => $query->orderByDesc('fecha_aplicacion')->paginate(20),
            'filters' => $request->only(['estado', 'cuartel_id', 'desde', 'hasta']),
        ]);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;

        return Inertia::render('agroquimicos/create', [
            'cuarteles' => Cuartel::where('tenant_id', $tenantId)->orderBy('nombre')->get(),
            'variedades' => Variedad::where('tenant_id', $tenantId)->orderBy('nombre')->get(),
            'productosSag' => ProductoSAG::with(['producto', 'usos'])->where('tenant_id', $tenantId)->where('estado_sag', 'autorizado')->orderBy('nombre_comercial')->get(),
            'aplicadores' => Aplicador::where('tenant_id', $tenantId)->activo()->capacitado()->orderBy('nombres')->get(),
            'equipos' => EquipoAplicacion::where('tenant_id', $tenantId)->activo()->orderBy('nombre')->get(),
            'lotes' => Lote::with('producto')->where('tenant_id', $tenantId)->disponible()->orderBy('codigo_lote')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cuartel_id' => 'required|uuid|exists:cuartels,id',
            'variedad_id' => 'nullable|uuid|exists:variedades,id',
            'temporada' => 'nullable|string|max:50',
            'superficie' => 'required|numeric|min:0.01',
            'fecha_aplicacion' => 'required|date',
            'hora_inicio' => 'nullable|string',
            'hora_termino' => 'nullable|string',
            'objetivo_tipo' => 'required|string|max:30',
            'objetivo_nombre' => 'nullable|string|max:255',
            'aplicador_id' => 'nullable|uuid|exists:aplicadores,id',
            'supervisor_id' => 'nullable|uuid|exists:users,id',
            'equipo_id' => 'nullable|uuid|exists:equipos_aplicacion,id',
            'observaciones' => 'nullable|string',

            'productos' => 'required|array|min:1',
            'productos.*.producto_sag_id' => 'required|uuid|exists:productos_sag,id',
            'productos.*.lote_id' => 'nullable|uuid|exists:lotes,id',
            'productos.*.dosis' => 'required|numeric|min:0',
            'productos.*.unidad_dosis' => 'required|string|max:20',
            'productos.*.cantidad_total' => 'required|numeric|min:0',
            'productos.*.volumen_agua' => 'nullable|numeric|min:0',

            'weather' => 'nullable|array',
            'weather.temperatura' => 'nullable|numeric',
            'weather.humedad' => 'nullable|numeric',
            'weather.viento_velocidad' => 'nullable|numeric',
            'weather.viento_direccion' => 'nullable|string|max:20',
            'weather.estado_general' => 'nullable|string|max:100',
            'weather.riesgo_deriva' => 'nullable|string|max:10',
            'weather.fuente' => 'nullable|string|max:20',

            'safety' => 'nullable|array',
            'safety.epp_guantes' => 'boolean',
            'safety.epp_mascarilla' => 'boolean',
            'safety.epp_overol' => 'boolean',
            'safety.epp_botas' => 'boolean',
            'safety.epp_proteccion_ocular' => 'boolean',
            'safety.senalizacion_instalada' => 'boolean',
            'safety.agua_emergencia' => 'nullable|boolean',
            'safety.observaciones' => 'nullable|string',

            'envases' => 'nullable|array',
            'envases.*.producto_sag_id' => 'required|uuid|exists:productos_sag,id',
            'envases.*.envases_usados' => 'required|integer|min:0',
            'envases.*.capacidad_envase' => 'nullable|numeric',
            'envases.*.triple_lavado' => 'boolean',
            'envases.*.almacenamiento_temporal' => 'nullable|string|max:255',

            'estado' => 'nullable|string|max:20',
        ]);

        $tenantId = Auth::user()->tenant_id;

        // Validate stock availability
        foreach ($validated['productos'] as $p) {
            if (!empty($p['lote_id'])) {
                $lote = Lote::findOrFail($p['lote_id']);
                if ($lote->cantidad_disponible < $p['cantidad_total']) {
                    return redirect()->back()->withErrors([
                        'productos' => "Stock insuficiente en lote {$lote->codigo_lote}: disponible {$lote->cantidad_disponible}, solicitado {$p['cantidad_total']}",
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $tenantId) {
            $codigo = 'AP-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $record = ApplicationRecord::create([
                'tenant_id' => $tenantId,
                'codigo' => $codigo,
                'cuartel_id' => $validated['cuartel_id'],
                'variedad_id' => $validated['variedad_id'] ?? null,
                'temporada' => $validated['temporada'] ?? null,
                'superficie' => $validated['superficie'],
                'fecha_aplicacion' => $validated['fecha_aplicacion'],
                'hora_inicio' => $validated['hora_inicio'] ?? null,
                'hora_termino' => $validated['hora_termino'] ?? null,
                'estado' => $validated['estado'] ?? 'ejecutada',
                'objetivo_tipo' => $validated['objetivo_tipo'],
                'objetivo_nombre' => $validated['objetivo_nombre'] ?? null,
                'responsable_id' => Auth::id(),
                'aplicador_id' => $validated['aplicador_id'] ?? null,
                'supervisor_id' => $validated['supervisor_id'] ?? null,
                'equipo_id' => $validated['equipo_id'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'creado_por' => Auth::id(),
            ]);

            foreach ($validated['productos'] as $p) {
                $productoSag = ProductoSAG::findOrFail($p['producto_sag_id']);
                $record->productos()->create([
                    'producto_sag_id' => $p['producto_sag_id'],
                    'lote_id' => $p['lote_id'] ?? null,
                    'dosis' => $p['dosis'],
                    'unidad_dosis' => $p['unidad_dosis'],
                    'cantidad_total' => $p['cantidad_total'],
                    'volumen_agua' => $p['volumen_agua'] ?? null,
                    'label_snapshot' => json_encode([
                        'nombre_comercial' => $productoSag->nombre_comercial,
                        'nro_autorizacion_sag' => $productoSag->nro_autorizacion_sag,
                        'ingrediente_activo' => $productoSag->ingrediente_activo,
                        'estado_sag' => $productoSag->estado_sag,
                    ]),
                ]);
            }

            if (!empty($validated['weather'])) {
                $record->clima()->create($validated['weather']);
            }

            if (!empty($validated['safety'])) {
                $record->seguridad()->create($validated['safety']);
            }

            if (!empty($validated['envases'])) {
                foreach ($validated['envases'] as $e) {
                    $record->envases()->create($e);
                }
            }

            if (($validated['estado'] ?? 'ejecutada') === 'aprobada') {
                $record->aprobar(Auth::id());
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Aplicación registrada correctamente.']);

        return redirect()->route('agroquimicos.index');
    }

    public function show(string $id)
    {
        $record = ApplicationRecord::with([
            'cuartel', 'variedad', 'responsable', 'aplicadorRel', 'supervisor', 'equipo',
            'productos.productoSAG', 'productos.lote',
            'clima', 'seguridad', 'envases',
            'creadoPor', 'aprobadoPor', 'anuladoPor',
        ])->findOrFail($id);

        return Inertia::render('agroquimicos/show', [
            'record' => $record,
        ]);
    }

    public function approve(string $id)
    {
        $record = ApplicationRecord::findOrFail($id);

        if ($record->estado !== 'ejecutada' && $record->estado !== 'en_revision') {
            return redirect()->back()->withErrors(['error' => 'Solo se pueden aprobar aplicaciones en estado ejecutada o en revisión.']);
        }

        $record->aprobar(Auth::id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Aplicación aprobada y stock descontado.']);

        return redirect()->back();
    }

    public function cancel(Request $request, string $id)
    {
        $request->validate(['motivo_anulacion' => 'required|string|max:500']);

        $record = ApplicationRecord::findOrFail($id);

        if ($record->estado === 'anulada') {
            return redirect()->back()->withErrors(['error' => 'La aplicación ya está anulada.']);
        }

        $record->anular(Auth::id(), $request->motivo_anulacion);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Aplicación anulada correctamente.']);

        return redirect()->back();
    }
}
