<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentroCosto;

class CentroCostoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = CentroCosto::where('activo', true);

        if ($agrupador = $user->getAgrupadorFilter()) {
            $query->where('agrupador', $agrupador);
        }

        $centros = $query->orderBy('codigo')
            ->get(['id', 'tenant_id', 'nombre', 'codigo', 'agrupador', 'parent_id', 'activo']);

        return response()->json($centros);
    }
}
