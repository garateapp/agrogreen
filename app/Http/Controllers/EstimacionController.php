<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cuartel;
use App\Models\Estimacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstimacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Estimacion::with(['cuartel.centroCosto', 'cuartel.especie']);

        if ($anho = $request->get('anho')) {
            $query->where('anho', $anho);
        }
        if ($cuartelId = $request->get('cuartel_id')) {
            $query->where('cuartel_id', $cuartelId);
        }
        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        $estimaciones = $query->orderBy('anho', 'desc')
            ->orderBy('fecha_estimacion', 'desc')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'cuartel_id' => $e->cuartel_id,
                'cuartel_nombre' => $e->cuartel?->nombre ?? '—',
                'especie' => $e->cuartel?->especie?->nombre ?? '—',
                'centro_costo' => $e->cuartel?->centroCosto?->nombre ?? '—',
                'anho' => $e->anho,
                'nombre' => $e->nombre,
                'kilos_estimados' => (float) $e->kilos_estimados,
                'fecha_estimacion' => $e->fecha_estimacion?->format('Y-m-d'),
                'estado' => $e->estado,
                'observaciones' => $e->observaciones,
            ]);

        $cuarteles = Cuartel::with('centroCosto', 'especie')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => sprintf('%s — %s (%s)', $c->nombre, $c->especie?->nombre ?? '—', $c->centroCosto?->nombre ?? '—'),
                'especie' => $c->especie?->nombre ?? '—',
                'hectareas' => (float) $c->superficie_hectareas,
                'centro_costo_id' => $c->centro_costo_id,
            ]);

        return Inertia::render('presupuesto/estimaciones', [
            'pageTitle' => 'Estimaciones de Cosecha',
            'estimaciones' => $estimaciones,
            'cuarteles' => $cuarteles,
            'filters' => $request->only(['anho', 'cuartel_id', 'estado']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cuartel_id' => 'required|string',
            'anho' => 'required|integer|min:2020|max:2099',
            'nombre' => 'required|string|max:255',
            'kilos_estimados' => 'required|numeric|min:0',
            'fecha_estimacion' => 'required|date',
            'estado' => 'nullable|string|in:borrador,confirmado',
            'observaciones' => 'nullable|string',
        ]);

        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['estado'] ??= 'borrador';

        Estimacion::create($data);

        return redirect()->back()->with('success', 'Estimación creada');
    }

    public function update(Request $request, string $id)
    {
        $estimacion = Estimacion::findOrFail($id);

        $data = $request->validate([
            'cuartel_id' => 'required|string',
            'anho' => 'required|integer|min:2020|max:2099',
            'nombre' => 'required|string|max:255',
            'kilos_estimados' => 'required|numeric|min:0',
            'fecha_estimacion' => 'required|date',
            'estado' => 'nullable|string|in:borrador,confirmado',
            'observaciones' => 'nullable|string',
        ]);

        $estimacion->update($data);

        return redirect()->back()->with('success', 'Estimación actualizada');
    }

    public function destroy(string $id)
    {
        Estimacion::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Estimación eliminada');
    }
}
