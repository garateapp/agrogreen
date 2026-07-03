<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncAttendanceRequest;
use App\Models\Actividad;
use App\Models\Cuartel;
use App\Models\Empleado;
use App\Models\FaenaEmpleado;
use App\Models\FaenaRegistro;
use App\Models\Tarjeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarjetaController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $request->validate(['codigo_qr' => 'required|string|max:20']);

        $tarjeta = Tarjeta::with('empleado')
            ->where('codigo_qr', $request->codigo_qr)
            ->first();

        if (! $tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }

        return response()->json([
            'id' => $tarjeta->id,
            'codigo_qr' => $tarjeta->codigo_qr,
            'sigla' => $tarjeta->sigla,
            'activo' => $tarjeta->activo,
            'asignada' => $tarjeta->empleado_id !== null,
            'empleado' => $tarjeta->empleado ? [
                'id' => $tarjeta->empleado->id,
                'nombre' => $tarjeta->empleado->nombre,
                'apellido' => $tarjeta->empleado->apellido,
                'rut' => $tarjeta->empleado->rut,
            ] : null,
        ]);
    }

    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'codigo_qr' => 'required|string|max:20',
            'empleado_id' => 'required|uuid|exists:empleados,id',
        ]);

        $user = auth()->user();
        $tarjeta = Tarjeta::where('codigo_qr', $request->codigo_qr)->firstOrFail();
        $empleado = Empleado::findOrFail($request->empleado_id);

        $tarjeta->assignTo($empleado, $user->id);

        $registro = DB::transaction(function () use ($tarjeta, $request, $user) {
            $faena = FaenaRegistro::create([
                'tenant_id' => $user->tenant_id,
                'fecha' => today()->toDateString(),
                'actividad_id' => null,
                'centro_costo_id' => null,
                'supervisor_id' => $user->id,
            ]);

            FaenaEmpleado::create([
                'faena_registro_id' => $faena->id,
                'empleado_id' => $tarjeta->empleado_id,
                'horas_trabajadas' => 0,
                'liquido_a_pagar' => 0,
            ]);

            return $faena;
        });

        return response()->json([
            'message' => 'Tarjeta asignada y asistencia registrada',
            'tarjeta' => [
                'id' => $tarjeta->id,
                'codigo_qr' => $tarjeta->codigo_qr,
                'empleado' => [
                    'id' => $empleado->id,
                    'nombre' => $empleado->nombre,
                    'apellido' => $empleado->apellido,
                ],
            ],
            'faena' => [
                'id' => $registro->id,
                'fecha' => $registro->fecha,
            ],
        ]);
    }

    public function registerAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'codigo_qr' => 'required|string|max:20',
            'actividad_id' => 'required|uuid|exists:actividades,id',
            'fecha' => 'nullable|date',
            'cuarteles_ids' => 'nullable|array',
            'cuarteles_ids.*' => 'uuid|exists:cuartels,id',
            'sync_id' => 'nullable|string|max:36',
        ]);

        $tarjeta = Tarjeta::where('codigo_qr', $request->codigo_qr)->firstOrFail();

        if (! $tarjeta->empleado_id) {
            return response()->json(['message' => 'La tarjeta no está asignada a ningún trabajador'], 422);
        }

        $user = auth()->user();
        $fecha = $request->fecha ?? today()->toDateString();

        $registro = DB::transaction(function () use ($tarjeta, $request, $user, $fecha) {
            $faena = FaenaRegistro::create([
                'tenant_id' => $user->tenant_id,
                'fecha' => $fecha,
                'actividad_id' => $request->actividad_id,
                'centro_costo_id' => null,
                'supervisor_id' => $user->id,
            ]);

            $faenaEmpleado = FaenaEmpleado::create([
                'faena_registro_id' => $faena->id,
                'empleado_id' => $tarjeta->empleado_id,
                'horas_trabajadas' => 0,
                'liquido_a_pagar' => 0,
                'sync_id' => $request->sync_id,
                'sync_status' => 'pendiente',
            ]);

            if ($request->cuarteles_ids) {
                $cuartelesQuery = Cuartel::whereIn('id', $request->cuarteles_ids);
                if ($agrupador = $user->getAgrupadorFilter()) {
                    $cuartelesQuery->whereHas('centroCosto', fn ($q) => $q->where('agrupador', $agrupador));
                }
                $faena->cuarteles()->sync($cuartelesQuery->pluck('id')->toArray());
            }

            return $faena;
        });

        return response()->json([
            'message' => 'Asistencia registrada correctamente',
            'faena' => [
                'id' => $registro->id,
                'fecha' => $registro->fecha,
                'actividad_id' => $registro->actividad_id,
            ],
        ]);
    }

    public function actualizarFaena(Request $request): JsonResponse
    {
        $request->validate([
            'faena_id' => 'required|uuid|exists:faenas_registro,id',
            'actividad_id' => 'required|uuid|exists:actividades,id',
            'cuarteles_ids' => 'nullable|array',
            'cuarteles_ids.*' => 'uuid|exists:cuartels,id',
        ]);

        $user = auth()->user();
        $faena = FaenaRegistro::where('id', $request->faena_id)
            ->where('supervisor_id', $user->id)
            ->firstOrFail();

        $faena->update(['actividad_id' => $request->actividad_id]);

        if ($request->cuarteles_ids) {
            $faena->cuarteles()->sync($request->cuarteles_ids);
        }

        return response()->json(['message' => 'Jornada actualizada correctamente']);
    }

    public function actividades(Request $request): JsonResponse
    {
        $actividades = Actividad::where('presupuestable', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'tipo_labor']);

        return response()->json($actividades);
    }

    public function asistencias(Request $request): JsonResponse
    {
        $user = auth()->user();
        $fecha = $request->fecha ?? today()->toDateString();

        $registros = FaenaRegistro::with([
            'faenaEmpleados.empleado:id,nombre,apellido,rut',
            'actividad:id,nombre,tipo_labor',
            'cuarteles:id,nombre',
        ])
            ->whereDate('fecha', $fecha)
            ->where('supervisor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($registros);
    }

    public function empleados(Request $request): JsonResponse
    {
        $empleados = Empleado::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellido', 'rut']);

        return response()->json($empleados);
    }

    public function sync(SyncAttendanceRequest $request): JsonResponse
    {
        $user = auth()->user();
        $results = [];

        foreach ($request->records as $record) {
            try {
                $result = DB::transaction(function () use ($record, $user) {
                    $tarjeta = Tarjeta::where('codigo_qr', $record['codigo_qr'])->first();

                    if (! $tarjeta || ! $tarjeta->empleado_id) {
                        return [
                            'sync_id' => $record['sync_id'] ?? null,
                            'status' => 'skipped',
                            'message' => 'Tarjeta no encontrada o no asignada',
                        ];
                    }

                    $faena = FaenaRegistro::create([
                        'tenant_id' => $user->tenant_id,
                        'fecha' => $record['fecha'],
                        'actividad_id' => $record['actividad_id'],
                        'centro_costo_id' => null,
                        'supervisor_id' => $user->id,
                    ]);

                    FaenaEmpleado::create([
                        'faena_registro_id' => $faena->id,
                        'empleado_id' => $tarjeta->empleado_id,
                        'horas_trabajadas' => 0,
                        'liquido_a_pagar' => 0,
                        'sync_id' => $record['sync_id'] ?? null,
                        'sync_status' => 'synced',
                    ]);

                    if (! empty($record['cuarteles_ids'])) {
                        $cuarteles = Cuartel::whereIn('id', $record['cuarteles_ids'])->get();
                        $faena->cuarteles()->sync($cuarteles->pluck('id')->toArray());
                    }

                    return [
                        'sync_id' => $record['sync_id'] ?? null,
                        'status' => 'ok',
                        'faena_id' => $faena->id,
                    ];
                });

                $results[] = $result;
            } catch (\Exception $e) {
                $results[] = [
                    'sync_id' => $record['sync_id'] ?? null,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => 'Sincronización completada',
            'results' => $results,
        ]);
    }
}
