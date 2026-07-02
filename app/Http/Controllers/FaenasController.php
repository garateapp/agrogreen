<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Actividad;
use App\Models\CentroCosto;
use App\Models\FaenaRegistro;
use App\Models\Empleado;
use App\Models\FaenaEmpleado;
use App\Models\Contratista;
use App\Models\Unidad;
use App\Models\User;


class FaenasController extends Controller
{
    public function tasks()
    {
        $actividades = Actividad::all();
        $centrosCosto = CentroCosto::all();

        $registros = FaenaRegistro::with([
            'faenaEmpleados.empleado',
            'actividad',
            'centroCosto',
        ])->orderBy('fecha', 'desc')->get();

        $items = $registros->flatMap(fn ($r) => $r->faenaEmpleados->map(fn ($e) => [
            'id' => $e->id,
            'fecha' => $r->fecha->format('Y-m-d'),
            'empleado' => $e->empleado?->nombre.' '.$e->empleado?->apellido,
            'jornada' => $e->horas_trabajadas.'hrs',
            'centroCosto' => $r->centroCosto?->nombre ?? '',
            'tipoFaena' => $r->actividad?->nombre ?? '',
            'descripcion' => '',
            'costo' => (float) ($e->liquido_a_pagar ?? 0),
        ]));

        return Inertia::render('faenas/tasks', [
            'pageTitle' => 'Faenas',
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
            'items' => $items,
        ]);
    }

    public function tasksCreation()
    {
        $actividades = Actividad::with('unidadMedida')->get();
        $centrosCosto = CentroCosto::all();
        $empleados = Empleado::all();
        $faenas = FaenaRegistro::all();
        $unidades = Unidad::orderBy('nombre')->get(['id', 'nombre', 'abreviacion']);
        $supervisores = User::orderBy('name')->get(['id', 'name', 'email']);
        return Inertia::render('faenas/tasks-creation', [
            'pageTitle' => 'Tarja Diaria',
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
            'empleados' => $empleados,
            'faenas' => $faenas,
            'unidades' => $unidades,
            'supervisores' => $supervisores,
        ]);
    }

    public function tasksCreationMobile()
    {
        return Inertia::render('faenas/tasks-creation-mobile', [
            'pageTitle' => 'Tarja Diaria Móvil',
        ]);
    }

    public function salaryReport()
    {
        $empleados = Empleado::all(['id', 'nombre', 'apellido', 'valor_dia_base']);

        $items = FaenaEmpleado::with('empleado', 'faenaRegistro')
            ->get()
            ->groupBy('empleado_id')
            ->map(fn ($emps, $eid) => [
                'id' => $eid,
                'nombre' => ($emp = $emps->first()->empleado)
                    ? $emp->nombre.' '.$emp->apellido
                    : 'Sin nombre',
                'costoEstimado' => round($emps->sum(fn ($e) => ($e->empleado?->valor_dia_base ?? 0) * ($e->horas_trabajadas / 8))),
                'costoEmpresa' => round($emps->sum('liquido_a_pagar')),
                'sueldoBase' => round($emps->sum(fn ($e) => ($e->empleado?->valor_dia_base ?? 0) * ($e->horas_trabajadas / 8))),
                'totalTratos' => round($emps->sum(fn ($e) => ($e->monto_bono ?? 0) + (($e->cantidad_unidades_producidas ?? 0) * ($e->valor_trato_unitario ?? 0)))),
                'sueldoDiario' => round($emps->avg(fn ($e) => ($e->empleado?->valor_dia_base ?? 0))),
            ])
            ->values()
            ->all();

        return Inertia::render('faenas/salary-report', [
            'pageTitle' => 'Reporte de Sueldos',
            'items' => $items,
            'empleados' => $empleados,
        ]);
    }

    public function additionalSalaryReport()
    {
        $items = FaenaEmpleado::with('empleado', 'faenaRegistro.actividad', 'faenaRegistro.centroCosto')
            ->where(function ($q) {
                $q->where('monto_bono', '>', 0)
                  ->orWhereNotNull('cantidad_unidades_producidas');
            })
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'fecha' => $e->faenaRegistro?->fecha?->format('Y-m-d') ?? '',
                'tipoTrato' => $e->cantidad_unidades_producidas
                    ? ($e->faenaRegistro?->actividad?->nombre ?? 'Trato').' (unidad)'
                    : 'Bono',
                'cantidad' => (float) ($e->cantidad_unidades_producidas ?? 1),
                'monto' => (float) ($e->valor_trato_unitario ?? $e->monto_bono ?? 0),
                'total' => (float) ($e->liquido_a_pagar ?? 0),
                'empleado' => $e->empleado?->nombre.' '.$e->empleado?->apellido,
                'centroCosto' => $e->faenaRegistro?->centroCosto?->nombre ?? '',
                'actividad' => $e->faenaRegistro?->actividad?->nombre ?? '',
            ])
            ->all();

        $totalTratos = collect($items)->sum('total');

        $empleados = Empleado::all(['id', 'nombre', 'apellido']);
        $actividades = Actividad::all(['id', 'nombre']);
        $centrosCosto = CentroCosto::all(['id', 'nombre']);

        return Inertia::render('faenas/additional-salary-report', [
            'pageTitle' => 'Reporte de Tratos',
            'items' => $items,
            'totalTratos' => $totalTratos,
            'empleados' => $empleados,
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
        ]);
    }

    public function tasksPerformance()
    {
        $actividades = Actividad::all(['id', 'nombre']);
        $centrosCosto = CentroCosto::all(['id', 'nombre']);
        $contratistas = Contratista::orderBy('nombre')->get(['id', 'nombre']);

        $registros = FaenaRegistro::with([
            'faenaEmpleados.empleado',
            'actividad',
            'centroCosto',
        ])->get();

        $items = $registros->flatMap(fn ($r) => $r->faenaEmpleados->map(fn ($e) => [
            'id' => $e->id,
            'fecha' => $r->fecha->format('Y-m-d'),
            'actividad' => $r->actividad?->nombre ?? '',
            'actividad_id' => $r->actividad_id,
            'centroCosto' => $r->centroCosto?->nombre ?? '',
            'centro_costo_id' => $r->centro_costo_id,
            'empleado_id' => $e->empleado_id,
            'contratista_id' => $e->empleado?->contratista_id,
            'contratista' => $e->empleado?->contratista?->nombre ?? ($e->empleado?->es_contratista ? $e->empleado?->nombre.' '.$e->empleado?->apellido : ''),
            'trabajadores' => 1,
            'unidadesProducidas' => (float) ($e->cantidad_unidades_producidas ?? 0),
            'diasHombre' => $e->horas_trabajadas / 8,
            'sueldoDia' => (float) ($e->empleado?->valor_dia_base ?? 0) * ($e->horas_trabajadas / 8),
            'sueldoTrato' => (float) (($e->cantidad_unidades_producidas ?? 0) * ($e->valor_trato_unitario ?? 0)),
            'costoTotal' => (float) ($e->liquido_a_pagar ?? 0),
        ]));

        $chartData = $items->groupBy('actividad')->map(fn ($g) => [
            'actividad' => $g->first()['actividad'],
            'unidadesProducidas' => round($g->sum('unidadesProducidas')),
            'diasHombre' => round($g->sum('diasHombre'), 1),
            'costoTotal' => round($g->sum('costoTotal')),
            'trabajadores' => $g->pluck('empleado_id')->unique()->count(),
        ])->values()->all();

        return Inertia::render('faenas/tasks-performance', [
            'pageTitle' => 'Rendimiento por Faenas',
            'items' => $items,
            'chartData' => $chartData,
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
            'contratistas' => $contratistas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'supervisor_id' => 'nullable|uuid',
            'empleados' => 'required|array|min:1',
            'empleados.*.empleado_id' => 'required|uuid',
            'empleados.*.actividad_id' => 'required|uuid',
            'empleados.*.centro_costo_id' => 'required|uuid',
            'empleados.*.jornada' => 'required|string',
            'empleados.*.horas_trabajadas' => 'required|numeric|min:0',
            'empleados.*.cantidad' => 'nullable|numeric|min:0',
            'empleados.*.monto_por_unidad' => 'nullable|numeric|min:0',
            'empleados.*.pago_total' => 'required|numeric|min:0',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $supervisorId = $validated['supervisor_id'] ?? auth()->id();

        DB::transaction(function () use ($validated, $tenantId, $supervisorId) {
            $groups = collect($validated['empleados'])->groupBy(fn ($e) => $e['actividad_id'].'|'.$e['centro_costo_id']);

            foreach ($groups as $rows) {
                $first = $rows->first();

                $registro = FaenaRegistro::create([
                    'tenant_id' => $tenantId,
                    'fecha' => $validated['fecha'],
                    'actividad_id' => $first['actividad_id'],
                    'centro_costo_id' => $first['centro_costo_id'],
                    'supervisor_id' => $supervisorId,
                ]);

                foreach ($rows as $empleado) {
                    FaenaEmpleado::create([
                        'faena_registro_id' => $registro->id,
                        'empleado_id' => $empleado['empleado_id'],
                        'horas_trabajadas' => $empleado['horas_trabajadas'],
                        'cantidad_unidades_producidas' => $empleado['cantidad'] ?: null,
                        'valor_trato_unitario' => $empleado['monto_por_unidad'] ?: null,
                        'liquido_a_pagar' => $empleado['pago_total'],
                        'monto_bono' => 0,
                        'sync_status' => 'pendiente',
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Tarja guardada correctamente.');
    }
}
