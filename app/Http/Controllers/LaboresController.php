<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\CentroCosto;
use App\Models\Cuartel;
use App\Models\Labor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaboresController extends Controller
{
    public function planificador(Request $request)
    {
        $query = Labor::with([
            'actividad',
            'centroCosto',
            'supervisor',
            'cuarteles',
            'empleados.empleado',
        ]);

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }
        if ($actividadId = $request->get('actividad_id')) {
            $query->where('actividad_id', $actividadId);
        }
        if ($cuartelId = $request->get('cuartel_id')) {
            $query->whereHas('cuarteles', fn ($q) => $q->where('cuartel_id', $cuartelId));
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('fecha_programada', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('fecha_programada', '<=', $dateTo);
        }

        $labores = $query->orderBy('fecha_programada', 'desc')
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'plantilla_id' => $l->plantilla_id,
                'actividad_id' => $l->actividad_id,
                'actividad' => $l->actividad?->nombre ?? '—',
                'icono' => $l->actividad?->icono ?? 'Agriculture',
                'color' => $l->actividad?->color ?? '#4CAF50',
                'tipo_labor' => $l->actividad?->tipo_labor ?? 'dia',
                'centro_costo_id' => $l->centro_costo_id,
                'centro_costo' => $l->centroCosto?->nombre.'-'.$l->centroCosto?->codigo ?? '—',
                'supervisor_id' => $l->supervisor_id,
                'supervisor' => $l->supervisor?->name ?? '—',
                'estado' => $l->estado,
                'fecha_programada' => $l->fecha_programada?->format('Y-m-d'),
                'fecha_ejecucion' => $l->fecha_ejecucion?->format('Y-m-d'),
                'fecha_fin_estimada' => $l->fecha_fin_estimada?->format('Y-m-d'),
                'observaciones' => $l->observaciones,
                'avance' => $l->avance ?? 0,
                'valor_trato_unitario' => $l->valor_trato_unitario ? (float) $l->valor_trato_unitario : null,
                'requiere_empleados' => $l->requiere_empleados,
                'es_ciclica' => $l->es_ciclica,
                'frecuencia' => $l->frecuencia,
                'fecha_fin_ciclo' => $l->fecha_fin_ciclo?->format('Y-m-d'),
                'inicio_real' => $l->inicio_real?->format('Y-m-d H:i'),
                'fin_real' => $l->fin_real?->format('Y-m-d H:i'),
                'cuarteles' => $l->cuarteles->map(fn ($c) => [
                    'id' => $c->id,
                    'nombre' => $c->nombre,
                ]),
                'empleados_count' => $l->empleados->count(),
                'empleados' => $l->empleados->map(fn ($e) => [
                    'id' => $e->id,
                    'empleado_id' => $e->empleado_id,
                    'nombre' => $e->empleado?->nombre . ' ' . $e->empleado?->apellido,
                    'horas_trabajadas' => (float) $e->horas_trabajadas,
                    'cantidad_unidades_producidas' => (float) $e->cantidad_unidades_producidas,
                    'valor_trato_unitario' => (float) $e->valor_trato_unitario,
                    'liquido_a_pagar' => (float) $e->liquido_a_pagar,
                ]),
            ]);

        $actividades = Actividad::orderBy('nombre')
            ->get(['id', 'nombre', 'icono', 'color', 'tipo_labor', 'unidad_medida_id', 'valor'])
            ->map(fn ($a) => [
                'value' => $a->id,
                'label' => $a->nombre,
                'icono' => $a->icono,
                'color' => $a->color,
                'tipo_labor' => $a->tipo_labor,
                'unidad_medida_id' => $a->unidad_medida_id,
                'valor' => $a->valor ? (float) $a->valor : null,
            ]);

        $centrosCosto = CentroCosto::orderBy('nombre')
            ->get(['id', 'nombre', 'codigo'])
            ->map(fn ($c) => ['value' => $c->id, 'label' => $c->nombre.'-'.$c->codigo]);

        $cuarteles = Cuartel::orderBy('nombre')
            ->get(['id', 'nombre', 'centro_costo_id'])
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => $c->nombre,
                'centro_costo_id' => $c->centro_costo_id,
            ]);

        return Inertia::render('labores/planificador', [
            'pageTitle' => 'Planificador de Labores',
            'labores' => $labores,
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
            'cuarteles' => $cuarteles,
            'filters' => $request->only(['estado', 'actividad_id', 'cuartel_id', 'date_from', 'date_to']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'actividad_id' => 'required|string',
            'centro_costo_id' => 'required|string',
            'fecha_programada' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date',
            'supervisor_id' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'avance' => 'nullable|integer|min:0|max:100',
            'valor_trato_unitario' => 'nullable|numeric|min:0',
            'requiere_empleados' => 'boolean',
            'es_ciclica' => 'boolean',
            'frecuencia' => 'nullable|string|in:none,diaria,semanal,quincenal,mensual',
            'fecha_fin_ciclo' => 'nullable|date',
            'cuarteles' => 'nullable|array',
            'cuarteles.*' => 'required|string',
        ]);

        if (empty($data['frecuencia'])) {
            $data['frecuencia'] = 'none';
        }
        if (!($data['es_ciclica'] ?? false)) {
            $data['frecuencia'] = 'none';
            $data['fecha_fin_ciclo'] = null;
        }

        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['estado'] = 'programada';

        $labor = Labor::create($data);

        if (!empty($data['cuarteles'])) {
            $labor->cuarteles()->attach($data['cuarteles']);
        }

        return redirect()->back()->with('success', 'Labor creada');
    }

    public function update(Request $request, string $id)
    {
        $labor = Labor::findOrFail($id);

        $data = $request->validate([
            'actividad_id' => 'required|string',
            'centro_costo_id' => 'required|string',
            'fecha_programada' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date',
            'supervisor_id' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'avance' => 'nullable|integer|min:0|max:100',
            'valor_trato_unitario' => 'nullable|numeric|min:0',
            'requiere_empleados' => 'boolean',
            'es_ciclica' => 'boolean',
            'frecuencia' => 'nullable|string|in:none,diaria,semanal,quincenal,mensual',
            'fecha_fin_ciclo' => 'nullable|date',
            'cuarteles' => 'nullable|array',
            'cuarteles.*' => 'required|string',
        ]);

        if (!($data['es_ciclica'] ?? false)) {
            $data['frecuencia'] = 'none';
            $data['fecha_fin_ciclo'] = null;
        }

        $labor->update($data);

        if (isset($data['cuarteles'])) {
            $labor->cuarteles()->sync($data['cuarteles']);
        }

        return redirect()->back()->with('success', 'Labor actualizada');
    }

    public function tarjaDiaria(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $labores = Labor::with([
            'actividad',
            'centroCosto',
            'supervisor',
            'cuarteles',
            'empleados.empleado',
        ])
            ->whereDate('fecha_programada', $date)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'actividad_id' => $l->actividad_id,
                'actividad' => $l->actividad?->nombre ?? '—',
                'icono' => $l->actividad?->icono ?? 'Agriculture',
                'color' => $l->actividad?->color ?? '#4CAF50',
                'tipo_labor' => $l->actividad?->tipo_labor ?? 'dia',
                'centro_costo_id' => $l->centro_costo_id,
                'centro_costo' => $l->centroCosto?->nombre ?? '—',
                'estado' => $l->estado,
                'fecha_programada' => $l->fecha_programada?->format('Y-m-d'),
                'fecha_fin_estimada' => $l->fecha_fin_estimada?->format('Y-m-d'),
                'observaciones' => $l->observaciones,
                'avance' => $l->avance ?? 0,
                'valor_trato_unitario' => $l->valor_trato_unitario ? (float) $l->valor_trato_unitario : null,
                'requiere_empleados' => $l->requiere_empleados,
                'cuarteles' => $l->cuarteles->map(fn ($c) => ['id' => $c->id, 'nombre' => $c->nombre]),
                'empleados' => $l->empleados->map(fn ($e) => [
                    'id' => $e->id,
                    'empleado_id' => $e->empleado_id,
                    'nombre' => $e->empleado?->nombre . ' ' . $e->empleado?->apellido,
                    'horas_trabajadas' => (float) $e->horas_trabajadas,
                    'cantidad_unidades_producidas' => (float) $e->cantidad_unidades_producidas,
                    'valor_trato_unitario' => (float) $e->valor_trato_unitario,
                    'liquido_a_pagar' => (float) $e->liquido_a_pagar,
                ]),
            ]);

        $actividades = Actividad::orderBy('nombre')
            ->get(['id', 'nombre', 'icono', 'color', 'tipo_labor', 'unidad_medida_id', 'valor'])
            ->map(fn ($a) => ['value' => $a->id, 'label' => $a->nombre, 'icono' => $a->icono, 'color' => $a->color, 'tipo_labor' => $a->tipo_labor, 'unidad_medida_id' => $a->unidad_medida_id, 'valor' => $a->valor ? (float) $a->valor : null]);

        $centrosCosto = CentroCosto::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn ($c) => ['value' => $c->id, 'label' => $c->nombre]);

        $cuarteles = Cuartel::orderBy('nombre')
            ->get(['id', 'nombre', 'centro_costo_id'])
            ->map(fn ($c) => ['value' => $c->id, 'label' => $c->nombre, 'centro_costo_id' => $c->centro_costo_id]);

        $empleados = \App\Models\Empleado::orderBy('nombre')
            ->get(['id', 'nombre', 'apellido'])
            ->map(fn ($e) => ['value' => $e->id, 'label' => $e->nombre . ' ' . $e->apellido]);

        return Inertia::render('labores/tarja-diaria', [
            'pageTitle' => 'Tarja Diaria',
            'labores' => $labores,
            'selectedDate' => $date,
            'actividades' => $actividades,
            'centrosCosto' => $centrosCosto,
            'cuarteles' => $cuarteles,
            'empleados' => $empleados,
        ]);
    }

    public function guardarTarja(Request $request, string $id)
    {
        $labor = Labor::findOrFail($id);

        $data = $request->validate([
            'empleados' => 'required|array',
            'empleados.*.empleado_id' => 'required|string',
            'empleados.*.horas_trabajadas' => 'nullable|numeric|min:0',
            'empleados.*.cantidad_unidades_producidas' => 'nullable|numeric|min:0',
            'empleados.*.valor_trato_unitario' => 'nullable|numeric|min:0',
            'empleados.*.liquido_a_pagar' => 'nullable|numeric|min:0',
        ]);

        $sentIds = [];

        foreach ($data['empleados'] as $empData) {
            $empleado = $labor->empleados()->updateOrCreate(
                ['empleado_id' => $empData['empleado_id']],
                [
                    'horas_trabajadas' => $empData['horas_trabajadas'] ?? 0,
                    'cantidad_unidades_producidas' => $empData['cantidad_unidades_producidas'] ?? 0,
                    'valor_trato_unitario' => $empData['valor_trato_unitario'] ?? 0,
                    'liquido_a_pagar' => $empData['liquido_a_pagar'] ?? 0,
                ],
            );
            $sentIds[] = $empleado->id;
        }

        $labor->empleados()->whereNotIn('id', $sentIds)->delete();

        return redirect()->back()->with('success', 'Tarja guardada');
    }

    public function destroy(string $id)
    {
        Labor::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Labor eliminada');
    }

    public function cambiarEstado(Request $request, string $id)
    {
        $labor = Labor::findOrFail($id);

        $data = $request->validate([
            'estado' => 'required|string|in:programada,en_curso,en_pausa,completada,realizada,cancelada',
        ]);

        $updates = ['estado' => $data['estado']];

        if ($data['estado'] === 'en_curso' && !$labor->inicio_real) {
            $updates['inicio_real'] = now();
        }
        if (in_array($data['estado'], ['realizada', 'completada'])) {
            $updates['fin_real'] = now();
            if (!$updates['fecha_ejecucion']) {
                $updates['fecha_ejecucion'] = now()->toDateString();
            }
        }

        $labor->update($updates);

        return redirect()->back()->with('success', 'Estado actualizado a ' . $data['estado']);
    }

    public function ejecutarInstancia(string $id)
    {
        $plantilla = Labor::findOrFail($id);

        if (!$plantilla->es_ciclica) {
            return redirect()->back()->with('error', 'Solo las plantillas cíclicas pueden generar instancias');
        }

        $instancia = Labor::create([
            'tenant_id' => $plantilla->tenant_id,
            'plantilla_id' => $plantilla->id,
            'actividad_id' => $plantilla->actividad_id,
            'centro_costo_id' => $plantilla->centro_costo_id,
            'supervisor_id' => $plantilla->supervisor_id,
            'estado' => 'programada',
            'fecha_programada' => now()->toDateString(),
            'observaciones' => $plantilla->observaciones,
            'avance' => 0,
            'valor_trato_unitario' => $plantilla->valor_trato_unitario,
            'requiere_empleados' => $plantilla->requiere_empleados,
            'es_ciclica' => false,
        ]);

        foreach ($plantilla->cuarteles as $cuartel) {
            $instancia->cuarteles()->attach($cuartel->id);
        }

        return redirect()->back()->with('success', 'Instancia generada desde plantilla');
    }
}
