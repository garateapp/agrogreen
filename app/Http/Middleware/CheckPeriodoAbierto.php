<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\PeriodoFiscal;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckPeriodoAbierto
{
    private const MUTABLE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next, string $dateField = 'fecha'): Response
    {
        if (!in_array($request->method(), self::MUTABLE_METHODS, true)) {
            return $next($request);
        }

        $dateValue = $request->input($dateField);

        if ($dateValue === null) {
            return $next($request);
        }

        $date = Carbon::parse($dateValue);

        $cerrado = PeriodoFiscal::where('tenant_id', auth()->user()->tenant_id)
            ->where('ano', $date->year)
            ->where('mes', $date->month)
            ->where('cerrado', true)
            ->exists();

        if ($cerrado) {
            throw new HttpException(
                403,
                'Operación denegada: El período fiscal correspondiente a esta fecha ya se encuentra cerrado de forma definitiva'
            );
        }

        return $next($request);
    }
}
