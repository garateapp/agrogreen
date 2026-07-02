<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\CentroCosto;
use App\Models\Cuartel;
use App\Models\Labor;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaboresSeeder extends Seeder
{
    public function run(?string $tenantId = null): void
    {
        $tid = $tenantId ?? Tenant::first()?->id ?? '00000000-0000-0000-0000-000000000001';
        $user = User::where('tenant_id', $tid)->first();

        $poda = Actividad::where('tenant_id', $tid)->where('nombre', 'Poda')->first();
        $riego = Actividad::where('tenant_id', $tid)->where('nombre', 'Ordenar línea de riego')->first();
        $cosecha = Actividad::where('tenant_id', $tid)->where('nombre', 'Cosecha')->first();
        $fertilizacion = Actividad::where('tenant_id', $tid)->where('nombre', 'Fertilización')->first();
        $limpieza = Actividad::where('tenant_id', $tid)->where('nombre', 'Limpieza')->first();

        $campoNorte = CentroCosto::where('tenant_id', $tid)->where('codigo', 'CC-CN')->first();
        $campoSur = CentroCosto::where('tenant_id', $tid)->where('codigo', 'CC-CS')->first();
        $admin = CentroCosto::where('tenant_id', $tid)->where('codigo', 'CC-ADM')->first();

        $cuarteles = Cuartel::where('tenant_id', $tid)->get();
        $cuartelNorte = $cuarteles->first();
        $cuartelSur = $cuarteles->skip(1)->first();

        // Plantilla cíclica: Poda quincenal en Campo Norte
        $plantillaPoda = Labor::create([
            'tenant_id' => $tid,
            'actividad_id' => $poda?->id,
            'centro_costo_id' => $campoNorte?->id,
            'supervisor_id' => $user?->id,
            'estado' => 'programada',
            'fecha_programada' => '2026-06-29',
            'fecha_fin_estimada' => '2026-06-29',
            'observaciones' => 'Poda de formación cada 15 días',
            'avance' => 0,
            'valor_trato_unitario' => 0,
            'requiere_empleados' => true,
            'es_ciclica' => true,
            'frecuencia' => 'quincenal',
            'fecha_fin_ciclo' => '2026-12-31',
        ]);
        if ($cuartelNorte) {
            $plantillaPoda->cuarteles()->attach($cuartelNorte->id);
        }

        // Instancia de la plantilla para el 29 de junio
        $instanciaPoda = Labor::create([
            'tenant_id' => $tid,
            'plantilla_id' => $plantillaPoda->id,
            'actividad_id' => $poda?->id,
            'centro_costo_id' => $campoNorte?->id,
            'supervisor_id' => $user?->id,
            'estado' => 'programada',
            'fecha_programada' => '2026-06-29',
            'fecha_fin_estimada' => '2026-06-29',
            'avance' => 0,
            'valor_trato_unitario' => 0,
            'requiere_empleados' => true,
            'es_ciclica' => false,
        ]);
        if ($cuartelNorte) {
            $instanciaPoda->cuarteles()->attach($cuartelNorte->id);
        }

        // Plantilla cíclica: Riego semanal en Campo Sur
        $plantillaRiego = Labor::create([
            'tenant_id' => $tid,
            'actividad_id' => $riego?->id,
            'centro_costo_id' => $campoSur?->id,
            'supervisor_id' => $user?->id,
            'estado' => 'programada',
            'fecha_programada' => '2026-06-22',
            'fecha_fin_estimada' => '2026-06-22',
            'observaciones' => 'Revisar y descolar riego cada semana',
            'avance' => 30,
            'requiere_empleados' => true,
            'es_ciclica' => true,
            'frecuencia' => 'semanal',
            'fecha_fin_ciclo' => '2026-09-30',
        ]);
        if ($cuartelSur) {
            $plantillaRiego->cuarteles()->attach($cuartelSur->id);
        }

        // Labor única: Cosecha en Campo Norte
        $laborCosecha = Labor::create([
            'tenant_id' => $tid,
            'actividad_id' => $cosecha?->id,
            'centro_costo_id' => $campoNorte?->id,
            'supervisor_id' => $user?->id,
            'estado' => 'programada',
            'fecha_programada' => '2026-07-15',
            'fecha_fin_estimada' => '2026-07-15',
            'observaciones' => 'Cosecha de temporada',
            'avance' => 0,
            'valor_trato_unitario' => 0,
            'requiere_empleados' => true,
            'es_ciclica' => false,
        ]);
        if ($cuartelNorte) {
            $laborCosecha->cuarteles()->attach($cuartelNorte->id);
        }

        // Labor única sin empleados: Fertilización con maquinaria
        $laborFertilizacion = Labor::create([
            'tenant_id' => $tid,
            'actividad_id' => $fertilizacion?->id,
            'centro_costo_id' => $campoSur?->id,
            'supervisor_id' => null,
            'estado' => 'programada',
            'fecha_programada' => '2026-07-01',
            'fecha_fin_estimada' => '2026-07-02',
            'observaciones' => 'Fertilización mecanizada',
            'avance' => 50,
            'requiere_empleados' => false,
            'es_ciclica' => false,
        ]);
        if ($cuartelSur) {
            $laborFertilizacion->cuarteles()->attach($cuartelSur->id);
        }

        // Labor única ya realizada: Limpieza
        $laborLimpieza = Labor::create([
            'tenant_id' => $tid,
            'actividad_id' => $limpieza?->id,
            'centro_costo_id' => $admin?->id,
            'supervisor_id' => $user?->id,
            'estado' => 'realizada',
            'fecha_programada' => '2026-06-20',
            'fecha_ejecucion' => '2026-06-20',
            'fecha_fin_estimada' => '2026-06-20',
            'observaciones' => 'Limpieza general instalaciones',
            'avance' => 100,
            'requiere_empleados' => true,
            'es_ciclica' => false,
            'inicio_real' => '2026-06-20 08:00:00',
            'fin_real' => '2026-06-20 17:00:00',
        ]);
        if ($cuartelNorte) {
            $laborLimpieza->cuarteles()->attach($cuartelNorte->id);
        }
    }
}
