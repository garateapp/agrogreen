<?php

namespace Database\Seeders;

use App\Models\Aplicador;
use App\Models\ApplicationRecord;
use App\Models\ApplicationRecordProducto;
use App\Models\ApplicationContainerDisposal;
use App\Models\ApplicationSafetyCheck;
use App\Models\ApplicationWeatherCondition;
use App\Models\ClasificacionAgroquimico;
use App\Models\EquipoAplicacion;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\ProductoSAG;
use App\Models\ProductoSAGUso;
use App\Models\Cuartel;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Database\Seeder;

class AgroquimicosSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ApplicationContainerDisposal::truncate();
        ApplicationSafetyCheck::truncate();
        ApplicationWeatherCondition::truncate();
        ApplicationRecordProducto::truncate();
        ApplicationRecord::truncate();
        ProductoSAGUso::truncate();
        ProductoSAG::truncate();
        Lote::truncate();
        Aplicador::truncate();
        EquipoAplicacion::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tenantId = \App\Models\Tenant::first()->id;
        $userId = User::first()->id;

        $clasif = ClasificacionAgroquimico::first();
        if (!$clasif) {
            return;
        }

        $unidad = \App\Models\Unidad::first();

        $producto1 = Producto::firstOrCreate([
            'tenant_id' => $tenantId,
            'nombre' => 'Glifosato 48% SL',
        ], [
            'categoria' => 'agroquimico',
            'unidad_medida_id' => $unidad->id,
            'ingrediente_activo' => 'Glifosato',
        ]);

        $producto2 = Producto::firstOrCreate([
            'tenant_id' => $tenantId,
            'nombre' => 'Cipermetrina 25% EC',
        ], [
            'categoria' => 'agroquimico',
            'unidad_medida_id' => $unidad->id,
            'ingrediente_activo' => 'Cipermetrina',
        ]);

        $ps1 = ProductoSAG::updateOrCreate([
            'producto_id' => $producto1->id,
        ], [
            'tenant_id' => $tenantId,
            'clasificacion_agroquimico_id' => $clasif->id,
            'nro_autorizacion_sag' => 'SAG-001-2025',
            'nombre_comercial' => 'Glifosato 48% SL',
            'ingrediente_activo' => 'Glifosato',
            'titular' => 'AgroQuim S.A.',
            'estado_sag' => 'autorizado',
            'toxicidad_abejas' => 'baja',
        ]);

        $ps2 = ProductoSAG::updateOrCreate([
            'producto_id' => $producto2->id,
        ], [
            'tenant_id' => $tenantId,
            'clasificacion_agroquimico_id' => $clasif->id,
            'nro_autorizacion_sag' => 'SAG-002-2025',
            'nombre_comercial' => 'Cipermetrina 25% EC',
            'ingrediente_activo' => 'Cipermetrina',
            'titular' => 'AgroQuim S.A.',
            'estado_sag' => 'autorizado',
            'toxicidad_abejas' => 'alta',
        ]);

        ProductoSAGUso::create([
            'producto_sag_id' => $ps1->id,
            'objetivo' => 'Malezas anuales y perennes',
            'dosis_min' => 2.0,
            'dosis_max' => 4.0,
            'unidad_dosis' => 'L/ha',
            'carencia_dias' => 0,
            'reingreso_horas' => 12,
        ]);

        ProductoSAGUso::create([
            'producto_sag_id' => $ps2->id,
            'objetivo' => 'Insectos masticadores y chupadores',
            'dosis_min' => 0.5,
            'dosis_max' => 1.0,
            'unidad_dosis' => 'L/ha',
            'carencia_dias' => 7,
            'reingreso_horas' => 24,
        ]);

        Lote::updateOrCreate(['codigo_lote' => 'LOTE-GLI-001'], [
            'tenant_id' => $tenantId,
            'producto_id' => $producto1->id,
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 200,
            'cantidad_disponible' => 180,
        ]);

        Lote::updateOrCreate(['codigo_lote' => 'LOTE-GLI-002'], [
            'tenant_id' => $tenantId,
            'producto_id' => $producto1->id,
            'fecha_vencimiento' => now()->addMonths(8),
            'cantidad_inicial' => 100,
            'cantidad_disponible' => 95,
        ]);

        Lote::updateOrCreate(['codigo_lote' => 'LOTE-CIP-001'], [
            'tenant_id' => $tenantId,
            'producto_id' => $producto2->id,
            'fecha_vencimiento' => now()->addMonths(6),
            'cantidad_inicial' => 50,
            'cantidad_disponible' => 42,
        ]);

        $ap1 = Aplicador::updateOrCreate(['rut' => '12.345.678-9'], [
            'tenant_id' => $tenantId,
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Martínez Rojas',
            'fecha_nacimiento' => '1990-05-15',
            'capacitado' => true,
            'activo' => true,
        ]);

        $ap2 = Aplicador::updateOrCreate(['rut' => '23.456.789-0'], [
            'tenant_id' => $tenantId,
            'nombres' => 'María Elena',
            'apellidos' => 'González Pérez',
            'fecha_nacimiento' => '1988-11-22',
            'capacitado' => true,
            'activo' => true,
        ]);

        EquipoAplicacion::updateOrCreate(['nombre' => 'Pulverizadora Jacto 2000L'], [
            'tenant_id' => $tenantId,
            'tipo' => 'pulverizadora',
            'ultima_calibracion' => now()->subMonth(),
            'proxima_calibracion' => now()->addMonths(5),
            'ultima_mantencion' => now()->subMonths(2),
            'proxima_mantencion' => now()->addMonths(4),
            'activo' => true,
        ]);

        EquipoAplicacion::updateOrCreate(['nombre' => 'Mochila Stihl SR 450'], [
            'tenant_id' => $tenantId,
            'tipo' => 'mochila',
            'ultima_calibracion' => now()->subMonths(2),
            'proxima_calibracion' => now()->addMonths(4),
            'activo' => true,
        ]);

        $cuartel = Cuartel::first();
        $variedad = Variedad::first();

        if ($cuartel && $variedad && $ap1) {
            $record = ApplicationRecord::create([
                'tenant_id' => $tenantId,
                'codigo' => 'AP-20260615-000001',
                'cuartel_id' => $cuartel->id,
                'variedad_id' => $variedad->id,
                'temporada' => '2025-2026',
                'superficie' => 12.5,
                'fecha_aplicacion' => now()->subDays(2),
                'hora_inicio' => '08:30',
                'hora_termino' => '11:45',
                'estado' => 'aprobada',
                'objetivo_tipo' => 'maleza',
                'objetivo_nombre' => 'Malezas de hoja ancha',
                'responsable_id' => $userId,
                'aplicador_id' => $ap1->id,
                'equipo_id' => EquipoAplicacion::first()->id,
                'observaciones' => 'Aplicación en parcela sur, condiciones favorables.',
                'creado_por' => $userId,
                'aprobado_por' => $userId,
                'aprobado_en' => now()->subDay(),
            ]);

            ApplicationRecordProducto::create([
                'application_record_id' => $record->id,
                'producto_sag_id' => $ps1->id,
                'lote_id' => Lote::first()->id,
                'dosis' => 2.5,
                'unidad_dosis' => 'L/ha',
                'cantidad_total' => 31.25,
                'volumen_agua' => 250,
            ]);

            ApplicationWeatherCondition::create([
                'application_record_id' => $record->id,
                'temperatura' => 22.5,
                'humedad' => 65,
                'viento_velocidad' => 8,
                'viento_direccion' => 'SO',
                'estado_general' => 'Despejado',
                'riesgo_deriva' => 'bajo',
                'fuente' => 'manual',
            ]);

            ApplicationSafetyCheck::create([
                'application_record_id' => $record->id,
                'epp_guantes' => true,
                'epp_mascarilla' => true,
                'epp_overol' => true,
                'epp_botas' => true,
                'epp_proteccion_ocular' => true,
                'senalizacion_instalada' => true,
            ]);

            ApplicationContainerDisposal::create([
                'application_record_id' => $record->id,
                'producto_sag_id' => $ps1->id,
                'envases_usados' => 2,
                'capacidad_envase' => 20,
                'triple_lavado' => true,
                'almacenamiento_temporal' => 'Bodega de residuos',
            ]);
        }
    }
}
