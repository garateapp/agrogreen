<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuartel;
use Illuminate\Http\JsonResponse;

class CuartelController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $query = Cuartel::with('especie:id,nombre');

        if ($agrupador = $user->getAgrupadorFilter()) {
            $query->whereHas('centroCosto', fn ($q) => $q->where('agrupador', $agrupador));
        }

        $cuarteles = $query->orderBy('nombre')
            ->get(['id', 'nombre', 'centro_costo_id', 'especie_id']);

        return response()->json($cuarteles);
    }
}
