<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\ConsumoMaquinaria;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\TractorMaquinaria;
use App\Models\UsoMaquinaria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaquinariaController extends Controller
{
    public function machineTasks()
    {
        return Inertia::render('maquinaria/machine-tasks', [
            'items' => UsoMaquinaria::with(['tractor', 'operador', 'centroCosto'])
                ->orderBy('fecha', 'desc')
                ->orderBy('created_at', 'desc')
                ->get(),
            'tractores' => TractorMaquinaria::orderBy('nombre')->get(['id', 'nombre', 'patente_o_identificador', 'tipo']),
            'empleados' => Empleado::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido']),
            'centrosCosto' => CentroCosto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function storeMachineTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tractor_id' => 'required|uuid|exists:tractores_maquinaria,id',
            'operador_id' => 'required|uuid|exists:empleados,id',
            'fecha' => 'required|date',
            'horas_inicio' => 'required|numeric|min:0',
            'horas_fin' => 'required|numeric|min:0',
            'centro_costo_id' => 'required|uuid|exists:centros_costo,id',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        UsoMaquinaria::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Faena de maquinaria creada correctamente.']);

        return redirect()->back();
    }

    public function updateMachineTask(Request $request, string $id): RedirectResponse
    {
        $uso = UsoMaquinaria::findOrFail($id);

        $validated = $request->validate([
            'tractor_id' => 'required|uuid|exists:tractores_maquinaria,id',
            'operador_id' => 'required|uuid|exists:empleados,id',
            'fecha' => 'required|date',
            'horas_inicio' => 'required|numeric|min:0',
            'horas_fin' => 'required|numeric|min:0',
            'centro_costo_id' => 'required|uuid|exists:centros_costo,id',
        ]);

        $uso->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Faena de maquinaria actualizada correctamente.']);

        return redirect()->back();
    }

    public function destroyMachineTask(string $id): RedirectResponse
    {
        UsoMaquinaria::findOrFail($id)->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Faena de maquinaria eliminada correctamente.']);

        return redirect()->back();
    }

    public function oilReceipts()
    {
        return Inertia::render('maquinaria/oil-receipts', [
            'items' => ConsumoMaquinaria::with(['usoMaquinaria.tractor', 'producto'])
                ->orderBy('created_at', 'desc')
                ->get(),
            'faenas' => UsoMaquinaria::with('tractor')
                ->orderBy('fecha', 'desc')
                ->get(['id', 'tractor_id', 'fecha']),
            'productos' => Producto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function storeOilReceipt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'uso_maquinaria_id' => 'required|uuid|exists:uso_maquinaria,id',
            'producto_id' => 'required|uuid|exists:productos,id',
            'cantidad_litros' => 'required|numeric|min:0',
            'costo_total_moneda_base' => 'required|numeric|min:0',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        ConsumoMaquinaria::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Salida registrada correctamente.']);

        return redirect()->back();
    }

    public function updateOilReceipt(Request $request, string $id): RedirectResponse
    {
        $consumo = ConsumoMaquinaria::findOrFail($id);

        $validated = $request->validate([
            'uso_maquinaria_id' => 'required|uuid|exists:uso_maquinaria,id',
            'producto_id' => 'required|uuid|exists:productos,id',
            'cantidad_litros' => 'required|numeric|min:0',
            'costo_total_moneda_base' => 'required|numeric|min:0',
        ]);

        $consumo->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Salida actualizada correctamente.']);

        return redirect()->back();
    }

    public function destroyOilReceipt(string $id): RedirectResponse
    {
        ConsumoMaquinaria::findOrFail($id)->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Salida eliminada correctamente.']);

        return redirect()->back();
    }

    public function machineReport()
    {
        $usos = UsoMaquinaria::with(['tractor', 'consumos'])->get();

        $grouped = $usos->groupBy('tractor_id');

        $items = $grouped->map(function ($usos, $tractorId) {
            $tractor = $usos->first()->tractor;
            $horas = $usos->sum('horas_totales');
            $costoConsumos = $usos->flatMap->consumos->sum('costo_total_moneda_base');

            return [
                'id' => $tractorId,
                'maquina' => $tractor->nombre,
                'patente' => $tractor->patente_o_identificador,
                'tipo' => $tractor->tipo,
                'horas' => round((float) $horas, 2),
                'costoConsumos' => round((float) $costoConsumos, 0),
                'costoTotal' => round((float) $costoConsumos, 0),
                'costoHora' => $horas > 0 ? round((float) ($costoConsumos / $horas), 0) : 0,
            ];
        })->values();

        return Inertia::render('maquinaria/machine-report', [
            'items' => $items,
            'tractores' => TractorMaquinaria::orderBy('nombre')->get(['id', 'nombre', 'patente_o_identificador', 'tipo']),
        ]);
    }
}
