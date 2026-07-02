<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class LiquidacionMensualDTO
{
    /**
     * @param  string  $empleadoId
     * @param  string  $rut
     * @param  string  $nombre
     * @param  string  $apellido
     * @param  string  $tipoContrato
     * @param  int  $mes
     * @param  int  $ano
     * @param  float  $totalDiasLaborados
     * @param  float  $totalHorasOrdinarias
     * @param  float  $totalHorasExtra
     * @param  float  $montoBase
     * @param  float  $montoBonos
     * @param  float  $montoTotalLiquido
     */
    public function __construct(
        public readonly string $empleadoId,
        public readonly string $rut,
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $tipoContrato,
        public readonly int $mes,
        public readonly int $ano,
        public readonly float $totalDiasLaborados,
        public readonly float $totalHorasOrdinarias,
        public readonly float $totalHorasExtra,
        public readonly float $montoBase,
        public readonly float $montoBonos,
        public readonly float $montoTotalLiquido,
    ) {}
}
