<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Bin;
use App\Models\ContenedorCosecha;
use App\Models\Cosecha;
use App\Models\Cuartel;
use App\Models\Empleado;
use App\Models\FaenaEmpleado;
use App\Models\FaenaRegistro;
use App\Models\Tarjeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CosechaController extends Controller
{
    private function getTenantId(): string
    {
        return auth()->user()->tenant_id;
    }

    // ===== API Methods =====

    public function storeCosecha(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_tarjeta_qr' => 'required|string|exists:tarjetas,codigo_qr',
            'cuartel_id' => 'required|uuid|exists:cuarteles,id',
            'contenedor_id' => 'required|uuid|exists:contenedores_cosecha,id',
            'peso_bruto' => 'required|numeric|min:0',
        ]);

        $tarjeta = Tarjeta::where('codigo_qr', $validated['codigo_tarjeta_qr'])
            ->where('activo', true)
            ->firstOrFail();

        $contenedor = ContenedorCosecha::findOrFail($validated['contenedor_id']);

        $peso_tara = $contenedor->peso_bin_kg;

        $cosecha = Cosecha::create([
            'tenant_id' => $this->getTenantId(),
            'fecha_hora' => now(),
            'cuartel_id' => $validated['cuartel_id'],
            'empleado_id' => $tarjeta->empleado_id,
            'jefe_cosecha_id' => auth()->id(),
            'contenedor_id' => $validated['contenedor_id'],
            'codigo_tarjeta_qr' => $validated['codigo_tarjeta_qr'],
            'peso_bruto' => $validated['peso_bruto'],
            'peso_tara' => $peso_tara,
            'peso_neto' => round((float) $validated['peso_bruto'] - (float) $peso_tara, 3),
            'sync_status' => 'sincronizado',
        ]);

        $cosecha->load(['empleado', 'cuartel', 'contenedor']);

        return response()->json($cosecha, 201);
    }

    public function indexCosechas(Request $request): JsonResponse
    {
        $query = Cosecha::with(['empleado', 'cuartel', 'contenedor'])
            ->orderBy('fecha_hora', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_hora', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hora', '<=', $request->fecha_hasta . ' 23:59:59');
        }
        if ($request->filled('cuartel_id')) {
            $query->where('cuartel_id', $request->cuartel_id);
        }
        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        $cosechas = $query->paginate($request->input('per_page', 50));

        return response()->json($cosechas);
    }

    public function storeBin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'folio' => 'nullable|string|max:255',
            'contenedor_cosecha_id' => 'required|uuid|exists:contenedores_cosecha,id',
        ]);

        if ($request->filled('folio')) {
            $bin = Bin::where('folio', $validated['folio'])->first();
            if ($bin) {
                if ($bin->estado === 'cerrado') {
                    $bin->update([
                        'estado' => 'abierto',
                        'fecha_apertura' => now(),
                        'fecha_cierre' => null,
                        'abierto_por' => auth()->id(),
                    ]);
                }
                $bin->load('contenedorCosecha');
                return response()->json($bin);
            }
        }

        if (! $request->filled('folio')) {
            $max = Bin::where('folio', 'like', 'BN-%')
                ->max(DB::raw("CAST(SUBSTRING(folio, 4) AS UNSIGNED)"));
            $validated['folio'] = 'BN-'.str_pad((string)((int) $max + 1), 5, '0', STR_PAD_LEFT);
        }

        $bin = Bin::create([
            'tenant_id' => $this->getTenantId(),
            'folio' => $validated['folio'],
            'contenedor_cosecha_id' => $validated['contenedor_cosecha_id'],
            'estado' => 'abierto',
            'fecha_apertura' => now(),
            'abierto_por' => auth()->id(),
        ]);

        $bin->load('contenedorCosecha');

        return response()->json($bin, 201);
    }

    public function indexBins(Request $request): JsonResponse
    {
        $query = Bin::with('contenedorCosecha')
            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $bins = $query->paginate($request->input('per_page', 50));

        return response()->json($bins);
    }

    public function cerrarBin(Bin $bin): JsonResponse
    {
        if ($bin->estado === 'cerrado') {
            return response()->json(['message' => 'El bin ya está cerrado.'], 422);
        }

        $bin->update([
            'estado' => 'cerrado',
            'fecha_cierre' => now(),
        ]);

        $bin->load('contenedorCosecha');

        return response()->json($bin);
    }

    public function listarContenedores(): JsonResponse
    {
        $contenedores = ContenedorCosecha::orderBy('nombre')
            ->get(['id', 'nombre', 'unidades_por_bin', 'peso_bin_kg']);

        return response()->json($contenedores);
    }

    public function listarCuarteles(): JsonResponse
    {
        $cuarteles = Cuartel::with('especie')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'especie_id']);

        return response()->json($cuarteles);
    }

    public function listarTarjetasActivas(): JsonResponse
    {
        $tarjetas = Tarjeta::with('empleado')
            ->where('activo', true)
            ->orderBy('codigo_qr')
            ->get(['id', 'codigo_qr', 'sigla', 'empleado_id']);

        return response()->json($tarjetas);
    }

    // ===== Web Methods =====

    public function registro(Request $request): Response
    {
        $query = Cosecha::with(['empleado', 'cuartel', 'contenedor'])
            ->orderBy('fecha_hora', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_hora', '>=', $request->fecha_desde);
        } else {
            $query->whereDate('fecha_hora', today());
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hora', '<=', $request->fecha_hasta . ' 23:59:59');
        }
        if ($request->filled('cuartel_id')) {
            $query->where('cuartel_id', $request->cuartel_id);
        }
        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        $cosechas = $query->get();

        $resumen = [
            'total_kilos' => (float) $cosechas->sum('peso_neto'),
            'total_envases' => $cosechas->count(),
            'empleados_unicos' => $cosechas->unique('empleado_id')->count(),
            'total_bruto' => (float) $cosechas->sum('peso_bruto'),
        ];

        $cuarteles = Cuartel::orderBy('nombre')->get(['id', 'nombre']);
        $empleados = Empleado::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido']);
        $contenedores = ContenedorCosecha::orderBy('nombre')->get(['id', 'nombre', 'peso_bin_kg']);

        return Inertia::render('cosecha/registro', [
            'items' => $cosechas,
            'resumen' => $resumen,
            'cuarteles' => $cuarteles,
            'empleados' => $empleados,
            'contenedores' => $contenedores,
            'filters' => $request->only(['fecha_desde', 'fecha_hasta', 'cuartel_id', 'empleado_id']),
        ]);
    }

    public function contenedores(Request $request): Response
    {
        $bins = Bin::with('contenedorCosecha')
            ->orderBy('created_at', 'desc')
            ->get();

        $resumenPorCuartel = Cosecha::with('cuartel')
            ->whereDate('fecha_hora', today())
            ->select('cuartel_id', DB::raw('COUNT(*) as total_envases'), DB::raw('SUM(peso_neto) as total_kilos'))
            ->groupBy('cuartel_id')
            ->get();

        $cuarteles = Cuartel::orderBy('nombre')->get(['id', 'nombre']);

        $resumenGeneral = [
            'total_bins_abiertos' => $bins->where('estado', 'abierto')->count(),
            'total_bins_cerrados' => $bins->where('estado', 'cerrado')->count(),
            'total_kilos_hoy' => (float) $resumenPorCuartel->sum('total_kilos'),
        ];

        return Inertia::render('cosecha/contenedores', [
            'bins' => $bins,
            'resumenPorCuartel' => $resumenPorCuartel,
            'cuarteles' => $cuarteles,
            'resumenGeneral' => $resumenGeneral,
        ]);
    }

    public function cerrarJornada(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId();

        $actividad = Actividad::whereRaw('LOWER(nombre) LIKE ?', ['%cosecha%'])->first();
        if (! $actividad) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'No hay actividad de Cosecha configurada. Crea una en Mantenedores > Actividades.']);
            return redirect()->back();
        }

        $fecha = $request->input('fecha', today()->toDateString());

        $cosechas = Cosecha::with('cuartel')
            ->whereDate('fecha_hora', $fecha)
            ->get();

        if ($cosechas->isEmpty()) {
            Inertia::flash('toast', ['type' => 'warning', 'message' => "No hay registros de cosecha para la fecha $fecha."]);
            return redirect()->back();
        }

        DB::transaction(function () use ($cosechas, $actividad, $tenantId, $fecha) {
            $groups = $cosechas->groupBy(fn ($c) => $c->empleado_id.'|'.($c->cuartel?->centro_costo_id ?? 'sin-cc'));

            foreach ($groups as $empleadoCosechas) {
                $first = $empleadoCosechas->first();
                $centroCostoId = $first->cuartel?->centro_costo_id;

                if (! $centroCostoId) {
                    continue;
                }

                $registro = FaenaRegistro::create([
                    'tenant_id' => $tenantId,
                    'fecha' => $fecha,
                    'actividad_id' => $actividad->id,
                    'centro_costo_id' => $centroCostoId,
                    'supervisor_id' => auth()->id(),
                ]);

                $totalEnvases = $empleadoCosechas->count();
                $totalKilos = $empleadoCosechas->sum('peso_neto');
                $valorUnitario = (float) ($actividad->valor ?? 0);

                FaenaEmpleado::create([
                    'faena_registro_id' => $registro->id,
                    'empleado_id' => $first->empleado_id,
                    'horas_trabajadas' => 0,
                    'cantidad_unidades_producidas' => $totalEnvases,
                    'valor_trato_unitario' => $valorUnitario,
                    'monto_bono' => 0,
                    'liquido_a_pagar' => $totalEnvases * $valorUnitario,
                    'sync_status' => 'pendiente',
                ]);
            }
        });

        $totalEmpleados = $cosechas->unique('empleado_id')->count();
        $totalEnvases = $cosechas->count();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Jornada cerrada: $totalEmpleados empleados, $totalEnvases envases procesados.",
        ]);

        return redirect()->back();
    }
}
