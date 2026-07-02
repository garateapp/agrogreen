<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LiquidacionMensualDTO;
use App\Models\Empleado;
use App\Models\FaenaEmpleado;
use Carbon\Carbon;

class RemuneracionService
{
    /**
     * Calcula la liquidación mensual de un empleado consolidando
     * todas sus faenas en el período.
     *
     * Fórmulas:
     * - Planta / Contratista → (total_horas / 8) * valor_dia_base + bonos
     * - Temporero (trato)    → unidades_producidas * valor_trato_unitario + bonos
     */
    public function calcularLiquidacionMensual(string $empleadoId, int $mes, int $ano): LiquidacionMensualDTO
    {
        $empleado = Empleado::findOrFail($empleadoId);

        $faenas = FaenaEmpleado::where('empleado_id', $empleadoId)
            ->whereHas('faenaRegistro', function ($q) use ($mes, $ano) {
                $q->whereMonth('fecha', $mes)
                    ->whereYear('fecha', $ano);
            })
            ->get();

        $totalHoras = (float) $faenas->sum('horas_trabajadas');
        $totalUnidades = (float) $faenas->sum('cantidad_unidades_producidas');
        $totalBonos = (float) $faenas->sum('monto_bono');

        $totalDiasLaborados = $faenas
            ->pluck('faena_registro_id')
            ->unique()
            ->count();

        $montoBase = match ($empleado->tipo_contrato) {
            'planta', 'contratista' => ($totalHoras / 8) * (float) $empleado->valor_dia_base,
            'temporero' => $totalUnidades * (float) ($faenas->first()?->valor_trato_unitario ?? 0),
            default => 0.0,
        };

        $montoExtra = match ($empleado->tipo_contrato) {
            'planta', 'contratista' => max(0, $totalHoras - ($totalDiasLaborados * 8))
                * (float) $empleado->valor_hora_extra,
            default => 0.0,
        };

        $montoTotal = $montoBase + $montoExtra + $totalBonos;

        return new LiquidacionMensualDTO(
            empleadoId: $empleado->id,
            rut: $empleado->rut,
            nombre: $empleado->nombre,
            apellido: $empleado->apellido,
            tipoContrato: $empleado->tipo_contrato,
            mes: $mes,
            ano: $ano,
            totalDiasLaborados: (float) $totalDiasLaborados,
            totalHorasOrdinarias: min($totalHoras, $totalDiasLaborados * 8),
            totalHorasExtra: max(0, $totalHoras - ($totalDiasLaborados * 8)),
            montoBase: round($montoBase, 2),
            montoBonos: round($totalBonos, 2),
            montoTotalLiquido: round($montoTotal, 2),
        );
    }
}
