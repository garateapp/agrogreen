<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CarenciaViolationException;
use App\Models\OrdenAplicacion;
use Carbon\Carbon;

class FitosanitarioService
{
    /**
     * Verifica que ninguna cosecha planificada intersecte con
     * los periodos de carencia de los productos aplicados.
     *
     * @throws CarenciaViolationException
     */
    public function verificarCarencia(OrdenAplicacion $orden): void
    {
        $orden->loadMissing([
            'ordenAplicacionProductos.producto',
            'ordenAplicacionCuarteles.cuartel',
        ]);

        $fechaAplicacion = Carbon::parse($orden->fecha_planificada);

        foreach ($orden->ordenAplicacionProductos as $item) {
            $producto = $item->producto;

            if ($producto->dias_carencia <= 0) {
                continue;
            }

            $finCarencia = $fechaAplicacion->copy()->addDays($producto->dias_carencia);

            foreach ($orden->ordenAplicacionCuarteles as $oc) {
                $cuartel = $oc->cuartel;

                $cosechaEnRiesgo = $cuartel->cosechas()
                    ->whereBetween('fecha_hora', [
                        $fechaAplicacion->toDateTimeString(),
                        $finCarencia->toDateTimeString(),
                    ])
                    ->exists();

                if ($cosechaEnRiesgo) {
                    throw new CarenciaViolationException(
                        sprintf(
                            'El producto "%s" (%d días de carencia) interfiere con cosechas en el cuartel "%s" entre %s y %s',
                            $producto->nombre,
                            $producto->dias_carencia,
                            $cuartel->nombre ?? $cuartel->id,
                            $fechaAplicacion->toDateString(),
                            $finCarencia->toDateString()
                        )
                    );
                }
            }
        }
    }

    /**
     * Calcula la cantidad total de insumo requerido para la orden.
     * Fórmula: hectáreas_totales * (mojamiento_l_ha / 100) * dosis_comercial_por_hl
     */
    public function calcularCantidadTotalInsumo(OrdenAplicacion $orden): float
    {
        $orden->loadMissing('ordenAplicacionCuarteles.cuartel');

        $hectareasTotales = $orden->ordenAplicacionCuarteles
            ->sum(fn ($oc) => (float) $oc->cuartel->superficie_hectareas);

        $mojamiento = (float) $orden->mojamiento_l_ha;

        return $orden->ordenAplicacionProductos
            ->sum(function ($item) use ($hectareasTotales, $mojamiento) {
                $dosis = (float) $item->dosis_comercial_por_hl;

                return $hectareasTotales * ($mojamiento / 100) * $dosis;
            });
    }
}
