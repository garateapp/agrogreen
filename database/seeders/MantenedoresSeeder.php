<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\ClasificacionAgroquimico;
use App\Models\Cliente;
use App\Models\CentroCosto;
use App\Models\Cosecha;
use App\Models\Cuartel;
use App\Models\Empleado;
use App\Models\Contratista;
use App\Models\ContenedorCosecha;
use App\Models\DireccionEnvio;
use App\Models\Feriado;
use App\Models\ImplementoSeguridad;
use App\Models\IngredienteActivo;
use App\Models\ItemGasto;
use App\Models\Jornada;
use App\Models\MetodoPago;
use App\Models\Nebulizadora;
use App\Models\Proveedor;
use App\Models\SectorRiego;
use App\Models\TipoDocumento;
use App\Models\Trato;
use App\Models\Unidad;
use App\Models\TractorMaquinaria;
use App\Models\Familia;
use App\Models\Especie;
use App\Models\Variedad;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MantenedoresSeeder extends Seeder
{
    public function run(?string $tenantId = null): void
    {
        $tid = $tenantId ?? Tenant::first()?->id ?? '00000000-0000-0000-0000-000000000001';



        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo Olivar', 'codigo' => 'BD002']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo El Carmen', 'codigo' => 'BD003']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo La Esperanza', 'codigo' => 'BD004']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo Paine', 'codigo' => 'BD005']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo Los Niches', 'codigo' => 'BD006']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo Las Cabras', 'codigo' => 'BD007']);
        Bodega::create(['tenant_id' => $tid, 'nombre' => 'Bodega Campo Santa Isabel', 'codigo' => 'BD017']);

        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Fertilizantes Foliar', 'codigo' => 'CAT1']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Fertilizantes Suelo', 'codigo' => 'CAT2']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Insecticidas', 'codigo' => 'CAT3']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Acaricidas', 'codigo' => 'CAT4']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Fungicidas', 'codigo' => 'CAT5']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Coadyuvante', 'codigo' => 'CAT6']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Miscelaneos', 'codigo' => 'CAT7']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Herbicidas', 'codigo' => 'CAT8']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Fitorreguladores', 'codigo' => 'CAT9']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Ingrediente Activo', 'codigo' => 'CAT10']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Humectantes', 'codigo' => 'CAT11']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Bioestimulante', 'codigo' => 'CAT12']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Ceras', 'codigo' => 'CAT13']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Detergentes', 'codigo' => 'CAT14']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Refigerantes', 'codigo' => 'CAT15']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Desinfectantes', 'codigo' => 'CAT16']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Otros Insumos', 'codigo' => 'CAT17']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Correctores De Carencias', 'codigo' => 'CAT18']);
        Categoria::create(['tenant_id' => $tid, 'nombre' => 'Insumos Campo', 'codigo' => 'CAT19']);

        ClasificacionAgroquimico::create(['tenant_id' => $tid, 'nombre' => 'Clase I (Extremadamente Tóxico)', 'descripcion' => 'Alta peligrosidad']);
        ClasificacionAgroquimico::create(['tenant_id' => $tid, 'nombre' => 'Clase II (Altamente Tóxico)', 'descripcion' => 'Requiere manejo especializado']);
        ClasificacionAgroquimico::create(['tenant_id' => $tid, 'nombre' => 'Clase III (Moderadamente Tóxico)', 'descripcion' => 'Uso con precaución']);
        ClasificacionAgroquimico::create(['tenant_id' => $tid, 'nombre' => 'Clase IV (Ligeramente Tóxico)', 'descripcion' => 'Bajo riesgo']);

        Cliente::create(['tenant_id' => $tid, 'rut' => '77.777.777-7', 'razon_social' => 'Exportadora del Sur Ltda.', 'email' => 'ventas@exportdelsur.cl', 'telefono' => '+56 9 1234 5678']);
        Cliente::create(['tenant_id' => $tid, 'rut' => '88.888.888-8', 'razon_social' => 'Comercializadora Norte SA', 'email' => 'info@comernorte.cl', 'telefono' => '+56 9 8765 4321']);
        Cliente::create(['tenant_id' => $tid, 'rut' => '99.999.999-9', 'razon_social' => 'Mercado Local SpA', 'email' => 'contacto@mercadolocal.cl']);



        DireccionEnvio::create(['tenant_id' => $tid, 'nombre' => 'Sede Central', 'direccion' => 'Av. Principal 123', 'ciudad' => 'Santiago', 'region' => 'Región Metropolitana']);
        DireccionEnvio::create(['tenant_id' => $tid, 'nombre' => 'Planta Procesadora', 'direccion' => 'Ruta 5 Sur Km 45', 'ciudad' => 'Rancagua', 'region' => "Región del Libertador General Bernardo O'Higgins"]);
        DireccionEnvio::create(['tenant_id' => $tid, 'nombre' => 'Campo Norte', 'direccion' => 'Camino Interior S/N', 'ciudad' => 'Los Andes', 'region' => 'Región de Valparaíso']);

        Feriado::create(['tenant_id' => $tid, 'nombre' => 'Año Nuevo', 'fecha' => '2026-01-01']);
        Feriado::create(['tenant_id' => $tid, 'nombre' => 'Viernes Santo', 'fecha' => '2026-04-03']);
        Feriado::create(['tenant_id' => $tid, 'nombre' => 'Día del Trabajador', 'fecha' => '2026-05-01']);
        Feriado::create(['tenant_id' => $tid, 'nombre' => 'Fiestas Patrias', 'fecha' => '2026-09-18']);
        Feriado::create(['tenant_id' => $tid, 'nombre' => 'Navidad', 'fecha' => '2026-12-25']);

        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Casco de Seguridad', 'codigo' => 'EPP-001']);
        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Lentes de Protección', 'codigo' => 'EPP-002']);
        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Guantes de Nitrilo', 'codigo' => 'EPP-003']);
        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Mascarilla Respiradora', 'codigo' => 'EPP-004']);
        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Overol Tyvek', 'codigo' => 'EPP-005']);
        ImplementoSeguridad::create(['tenant_id' => $tid, 'nombre' => 'Botas de Goma', 'codigo' => 'EPP-006']);

        IngredienteActivo::create(['tenant_id' => $tid, 'nombre' => 'Glifosato', 'descripcion' => 'Herbicida sistémico no selectivo']);
        IngredienteActivo::create(['tenant_id' => $tid, 'nombre' => 'Clorpirifos', 'descripcion' => 'Insecticida organofosforado']);
        IngredienteActivo::create(['tenant_id' => $tid, 'nombre' => 'Azufre', 'descripcion' => 'Fungicida inorgánico']);
        IngredienteActivo::create(['tenant_id' => $tid, 'nombre' => 'Abamectina', 'descripcion' => 'Insecticida/acaricida biológico']);

        $IGG001 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'MO', 'codigo' => 'IG-001']);
        $IGG002 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Combustible', 'codigo' => 'IG-002']);
        $IGG003 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Maquinaria', 'codigo' => 'IG-003']);
        $IGG004 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Arriendo Maquinaria', 'codigo' => 'IG-004']);
        $IGG005 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Riego', 'codigo' => 'IG-005']);
        $IGG006 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Materiales', 'codigo' => 'IG-006']);
        $IGG007 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Electricidad', 'codigo' => 'IG-007']);
        $IGG008 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Agua Riego - Canalista', 'codigo' => 'IG-008']);
        $IGG009 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Agua Potable', 'codigo' => 'IG-009']);
        $IGG010 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Arriendo Predios', 'codigo' => 'IG-010']);
        $IGG011 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Polinizacion', 'codigo' => 'IG-011']);
        $IGG012 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Comunicaciones y seguridad', 'codigo' => 'IG-012']);
        $IGG013 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Mantencion Activos', 'codigo' => 'IG-013']);
        $IGG014 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Gastos Imprenta', 'codigo' => 'IG-014']);
        $IGG015 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Analisis de Suelo', 'codigo' => 'IG-015']);
        $IGG016 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Costos Certificacion', 'codigo' => 'IG-016']);
        $IGG017 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Leasing', 'codigo' =>'IG-017']);
        $IGG018 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Utiles de Oficina', 'codigo' => 'IG-018']);
        $IGG019 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Implementos seguridad', 'codigo' => 'IG-019']);
        $IGG020 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Gastos Caja chica', 'codigo' => 'IG-020']);
        $IGG021 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Sueldos Administración', 'codigo' => 'IG-021']);
        $IGG022 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Mantención Caminos', 'codigo' => 'IG-022']);
        $IGG023 = ItemGasto::create(['tenant_id' => $tid, 'nombre' => 'Agroquimicos y fertilizantes', 'codigo' => 'IG-023']);

        $unidadKg = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Kilogramo', 'abreviacion' => 'kg']);
        $unidadL = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Litro', 'abreviacion' => 'L']);
        $unidadU = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Unidad', 'abreviacion' => 'u']);
        $unidadBnd = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Bandeja', 'abreviacion' => 'bnd']);
        $unidadBin = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Bin', 'abreviacion' => 'bin']);
        $unidadHa = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Hectárea', 'abreviacion' => 'ha']);
        $unidadPl = Unidad::create(['tenant_id' => $tid, 'nombre' => 'Planta', 'abreviacion' => 'pl']);

        $actividadesData = [
            ['nombre' => 'Repase amarras', 'icono' => 'Handyman', 'color' => '#795548'],
            ['nombre' => 'Poda verano', 'icono' => 'ContentCut', 'color' => '#4CAF50'],
            ['nombre' => 'Cosecha', 'icono' => 'Agriculture', 'color' => '#FF9800'],
            ['nombre' => 'Amarra', 'icono' => 'Cable', 'color' => '#607D8B'],
            ['nombre' => 'Estructura', 'icono' => 'Construction', 'color' => '#9E9E9E'],
            ['nombre' => 'Colocar cintas laterales', 'icono' => 'Cable', 'color' => '#607D8B'],
            ['nombre' => 'MO Planta', 'icono' => 'Engineering', 'color' => '#795548'],
            ['nombre' => 'Amarra y Desbrota', 'icono' => 'Cable', 'color' => '#607D8B'],
            ['nombre' => 'Pintura de troncos', 'icono' => 'FormatPaint', 'color' => '#F5F5F5'],
            ['nombre' => 'Descubrir cobertor Invierno', 'icono' => 'Deck', 'color' => '#90A4AE'],
            ['nombre' => 'Poda Producción', 'icono' => 'ContentCut', 'color' => '#388E3C'],
            ['nombre' => 'Poda', 'icono' => 'ContentCut', 'color' => '#4CAF50'],
            ['nombre' => 'Poda formación', 'icono' => 'ContentCut', 'color' => '#66BB6A'],
            ['nombre' => 'Mantención estructura', 'icono' => 'Build', 'color' => '#9E9E9E'],
            ['nombre' => 'Mantención estructura (Techo)', 'icono' => 'Roofing', 'color' => '#9E9E9E'],
            ['nombre' => 'Ordenar línea de riego', 'icono' => 'WaterDrop', 'color' => '#2196F3'],
            ['nombre' => 'Raleo yemas', 'icono' => 'Yard', 'color' => '#8BC34A'],
            ['nombre' => 'Cubrir cobertor Invierno', 'icono' => 'Deck', 'color' => '#90A4AE'],
            ['nombre' => 'Colocar colihues', 'icono' => 'Construction', 'color' => '#8D6E63'],
            ['nombre' => 'Manejo de techos', 'icono' => 'Roofing', 'color' => '#9E9E9E'],
            ['nombre' => 'Desbrote', 'icono' => 'Yard', 'color' => '#8BC34A'],
            ['nombre' => 'Desoje', 'icono' => 'Yard', 'color' => '#689F38'],
            ['nombre' => 'Raleo', 'icono' => 'Yard', 'color' => '#8BC34A'],
            ['nombre' => 'Colocar color Up', 'icono' => 'FormatPaint', 'color' => '#F5F5F5'],
            ['nombre' => 'Cubrir cobertor Primavera', 'icono' => 'Deck', 'color' => '#66BB6A'],
            ['nombre' => 'Limpieza', 'icono' => 'CleaningServices', 'color' => '#BDBDBD'],
            ['nombre' => 'Estructura mantención', 'icono' => 'Build', 'color' => '#9E9E9E'],
            ['nombre' => 'Incisiones de yemas', 'icono' => 'ContentCut', 'color' => '#7CB342'],
            ['nombre' => 'Colocar cintas de conducción', 'icono' => 'Cable', 'color' => '#607D8B'],
            ['nombre' => 'Ordenar cinta de riego', 'icono' => 'WaterDrop', 'color' => '#2196F3'],
            ['nombre' => 'Mejoras estructura', 'icono' => 'Construction', 'color' => '#9E9E9E'],
            ['nombre' => 'Desbrote y amarra', 'icono' => 'Yard', 'color' => '#689F38'],
            ['nombre' => 'Raleo fruta', 'icono' => 'Yard', 'color' => '#7CB342'],
            ['nombre' => 'Amarra laterales', 'icono' => 'Cable', 'color' => '#607D8B'],
            ['nombre' => 'Descole', 'icono' => 'Yard', 'color' => '#8BC34A'],
            ['nombre' => 'Repase coligues', 'icono' => 'Handyman', 'color' => '#795548'],
            ['nombre' => 'Corte de sierpes', 'icono' => 'ContentCut', 'color' => '#4CAF50'],
            ['nombre' => 'Fertilización', 'icono' => 'Yard', 'color' => '#43A047'],
            ['nombre' => 'Revisar y descole riego', 'icono' => 'WaterDrop', 'color' => '#2196F3'],
            ['nombre' => 'Limpieza de perímetros y acequias', 'icono' => 'CleaningServices', 'color' => '#BDBDBD'],
            ['nombre' => 'Colocar línea de riego', 'icono' => 'WaterDrop', 'color' => '#1976D2'],
            ['nombre' => 'Colocar protectores de herbicida', 'icono' => 'Shield', 'color' => '#FF5722'],
            ['nombre' => 'Aplicación herbicida', 'icono' => 'BugReport', 'color' => '#FF5722'],
            ['nombre' => 'Realizar estructura', 'icono' => 'Construction', 'color' => '#9E9E9E'],
            ['nombre' => 'Colocar cintas estructura', 'icono' => 'Cable', 'color' => '#607D8B'],
        ];

        $tratoActividades = [
            'Cosecha' => $unidadBin->id,
            'Poda' => $unidadPl->id,
            'Colocar colihues' => $unidadPl->id,
            'Raleo' => $unidadPl->id,
            'Desbrote' => $unidadPl->id,
        ];

        foreach ($actividadesData as $a) {
            $isTrato = isset($tratoActividades[$a['nombre']]);
            Actividad::create([
                'tenant_id' => $tid,
                'nombre' => $a['nombre'],
                'icono' => $a['icono'],
                'color' => $a['color'],
                'tipo_labor' => $isTrato ? 'trato' : 'dia',
                'unidad_medida_id' => $isTrato ? $tratoActividades[$a['nombre']] : null,
                'valor' => $isTrato ? 0 : 37500,
                'requiere_maquinaria' => in_array($a['nombre'], ['Fertilización', 'Aplicación herbicida']),
                'item_gasto_id' => $IGG001->id,
            ]);
        }



        Jornada::create(['tenant_id' => $tid, 'nombre' => 'Jornada Diurna', 'horas_jornada' => 8.00]);
        Jornada::create(['tenant_id' => $tid, 'nombre' => 'Jornada Parcial', 'horas_jornada' => 4.00]);
        Jornada::create(['tenant_id' => $tid, 'nombre' => 'Jornada Nocturna', 'horas_jornada' => 7.00]);

        MetodoPago::create(['tenant_id' => $tid, 'nombre' => 'Transferencia Electrónica']);
        MetodoPago::create(['tenant_id' => $tid, 'nombre' => 'Cheque']);
        MetodoPago::create(['tenant_id' => $tid, 'nombre' => 'Efectivo']);
        MetodoPago::create(['tenant_id' => $tid, 'nombre' => 'Tarjeta de Crédito']);
        MetodoPago::create(['tenant_id' => $tid, 'nombre' => 'Tarjeta de Débito']);

        Nebulizadora::create(['tenant_id' => $tid, 'nombre' => 'Nebulizadora Maruyama 2000', 'patente' => 'NB-001', 'capacidad_litros' => 2000]);
        Nebulizadora::create(['tenant_id' => $tid, 'nombre' => 'Nebulizadora Jacto 1500', 'patente' => 'NB-002', 'capacidad_litros' => 1500]);
        Nebulizadora::create(['tenant_id' => $tid, 'nombre' => 'Turbo Nebulizadora 3000', 'patente' => 'NB-003', 'capacidad_litros' => 3000]);

        Proveedor::create(['tenant_id' => $tid, 'rut' => '66.666.666-6', 'razon_social' => 'AgroInsumos Chile Ltda.', 'clasificacion' => 'Insumos', 'contacto_email' => 'ventas@agroinsumos.cl']);
        Proveedor::create(['tenant_id' => $tid, 'rut' => '55.555.555-5', 'razon_social' => 'Maquinarias del Sur SA', 'clasificacion' => 'Maquinaria', 'contacto_email' => 'contacto@maquinariasdelsur.cl']);
        Proveedor::create(['tenant_id' => $tid, 'rut' => '44.444.444-4', 'razon_social' => 'Fertilizantes y Más SpA', 'clasificacion' => 'Fertilizantes', 'contacto_email' => 'pedidos@fertimas.cl']);
        Proveedor::create(['tenant_id' => $tid, 'rut' => '33.333.333-3', 'razon_social' => 'Envases Agrícolas Ltda.', 'clasificacion' => 'Envases', 'contacto_email' => 'info@envasesagricolas.cl']);

        SectorRiego::create(['tenant_id' => $tid, 'nombre' => 'Sector Norte', 'caudal_disponible_l_s' => 120.00]);
        SectorRiego::create(['tenant_id' => $tid, 'nombre' => 'Sector Sur', 'caudal_disponible_l_s' => 85.50]);
        SectorRiego::create(['tenant_id' => $tid, 'nombre' => 'Sector Este', 'caudal_disponible_l_s' => 95.00]);
        SectorRiego::create(['tenant_id' => $tid, 'nombre' => 'Sector Oeste', 'caudal_disponible_l_s' => 110.25]);

        TipoDocumento::create(['tenant_id' => $tid, 'nombre' => 'Factura Electrónica', 'codigo_sii' => '33']);
        TipoDocumento::create(['tenant_id' => $tid, 'nombre' => 'Boleta Electrónica', 'codigo_sii' => '39']);
        TipoDocumento::create(['tenant_id' => $tid, 'nombre' => 'Guía de Despacho', 'codigo_sii' => '52']);
        TipoDocumento::create(['tenant_id' => $tid, 'nombre' => 'Nota de Crédito', 'codigo_sii' => '61']);
        TipoDocumento::create(['tenant_id' => $tid, 'nombre' => 'Nota de Débito', 'codigo_sii' => '56']);

        Trato::create(['tenant_id' => $tid, 'nombre' => 'Pago por Cosecha', 'codigo' => 'TR-001', 'tipo_trato' => 'cajas', 'unidad_medida' => 'bin']);
        Trato::create(['tenant_id' => $tid, 'nombre' => 'Bono de Producción', 'codigo' => 'TR-002', 'tipo_trato' => 'monto', 'bonificacion' => true]);
        Trato::create(['tenant_id' => $tid, 'nombre' => 'Hora Extra', 'codigo' => 'TR-003', 'tipo_trato' => 'monto', 'hora_extra' => true]);
        Trato::create(['tenant_id' => $tid, 'nombre' => 'Pago por Poda', 'codigo' => 'TR-004', 'tipo_trato' => 'monto', 'no_agrupar_actividad' => true]);
        Trato::create(['tenant_id' => $tid, 'nombre' => 'Bono Asistencia', 'codigo' => 'TR-005', 'tipo_trato' => 'monto', 'bonificacion' => true, 'depende_jornada' => true]);

        TractorMaquinaria::create(['tenant_id' => $tid, 'nombre' => 'Tractor John Deere 5075E', 'patente_o_identificador' => 'TR-001', 'tipo' => 'tractor', 'horas_motor_iniciales' => 1250, 'consumo_estimado_combustible_hora' => 8.5]);
        TractorMaquinaria::create(['tenant_id' => $tid, 'nombre' => 'Tractor New Holland TT4030', 'patente_o_identificador' => 'TR-002', 'tipo' => 'tractor', 'horas_motor_iniciales' => 3400, 'consumo_estimado_combustible_hora' => 9.2]);
        TractorMaquinaria::create(['tenant_id' => $tid, 'nombre' => 'Rastra de Discos 24', 'patente_o_identificador' => 'RS-001', 'tipo' => 'rastra', 'horas_motor_iniciales' => 500, 'consumo_estimado_combustible_hora' => 5.0]);
        TractorMaquinaria::create(['tenant_id' => $tid, 'nombre' => 'Camión Mercedes Benz', 'patente_o_identificador' => 'CB-001', 'tipo' => 'vehiculo_carga', 'horas_motor_iniciales' => 8900, 'consumo_estimado_combustible_hora' => 15.0]);

        $carozos = Familia::create(['tenant_id' => $tid, 'nombre' => 'Carozos', 'descripcion' => 'Familia de los carozos - ']);
        $pomaceas = Familia::create(['tenant_id' => $tid, 'nombre' => 'Pomaceas', 'descripcion' => 'Familia de las Pomaceas']);
        $citricos = Familia::create(['tenant_id' => $tid, 'nombre' => 'Citricos', 'descripcion' => 'Familia de los Citricos']);
        $vitaceas = Familia::create(['tenant_id' => $tid, 'nombre' => 'Vitáceas', 'descripcion' => 'Familia de las vitáceas - vides']);
        $granos = Familia::create(['tenant_id' => $tid, 'nombre' => 'Granos', 'descripcion' => 'Familia de los granos - frutos granulares']);
        $kiwi= Familia::create(['tenant_id' => $tid, 'nombre' => 'Kiwi', 'descripcion' => 'Familia de los actinidáceas - kiwis']);
        $solanaceas = Familia::create(['tenant_id' => $tid, 'nombre' => 'Solanáceas', 'descripcion' => 'Familia de las solanáceas - tomates, papas, pimientos']);
        $palta= Familia::create(['tenant_id' => $tid, 'nombre' => 'Palta', 'descripcion' => 'Familia de las palta']);
        $berries= Familia::create(['tenant_id' => $tid, 'nombre' => 'Berries', 'descripcion' => 'Familia de las berries - frambuesas, arándanos, moras']);
        $ebanaceas= Familia::create(['tenant_id' => $tid, 'nombre' => 'Ebanáceas', 'descripcion' => 'Familia de las ebanáceas - caquis']);
        $litraceas= Familia::create(['tenant_id' => $tid, 'nombre' => 'Litráceas', 'descripcion' => 'Familia de las litráceas - granadas']);

        $cherries = Especie::create(['tenant_id' => $tid, 'familia_id' => $carozos->id, 'nombre' => 'Cerezas', 'descripcion' => 'Prunus avium']);
        $apples = Especie::create(['tenant_id' => $tid, 'familia_id' => $pomaceas->id, 'nombre' => 'Manzanas', 'descripcion' => 'Malus domestica']);
        $peaches = Especie::create(['tenant_id' => $tid, 'familia_id' => $carozos->id, 'nombre' => 'Duraznos', 'descripcion' => 'Prunus persica']);
        $naranja = Especie::create(['tenant_id' => $tid, 'familia_id' => $citricos->id, 'nombre' => 'Naranjas', 'descripcion' => 'Citrus sinensis']);
        $limon = Especie::create(['tenant_id' => $tid, 'familia_id' => $citricos->id, 'nombre' => 'Limones', 'descripcion' => 'Citrus limon']);
        $tomate = Especie::create(['tenant_id' => $tid, 'familia_id' => $solanaceas->id, 'nombre' => 'Tomates', 'descripcion' => 'Solanum lycopersicum']);
        $plums= Especie::create(['tenant_id' => $tid, 'familia_id' => $carozos->id, 'nombre' => 'Plums', 'descripcion' => 'Prunus domestica']);
        $nectarines= Especie::create(['tenant_id' => $tid, 'familia_id' => $carozos->id, 'nombre' => 'Nectarines', 'descripcion' => 'Prunus persica']);
        $kiwi = Especie::create(['tenant_id' => $tid, 'familia_id' => $kiwi->id, 'nombre' => 'Kiwi', 'descripcion' => 'Actinidia deliciosa']);
        $pera = Especie::create(['tenant_id' => $tid, 'familia_id' => $pomaceas->id, 'nombre' => 'Pera', 'descripcion' => 'Pyrus communis']);
        $membrillo = Especie::create(['tenant_id' => $tid, 'familia_id' => $pomaceas->id, 'nombre' => 'Membrillo', 'descripcion' => 'Cydonia oblonga']);
        $palta = Especie::create(['tenant_id' => $tid, 'familia_id' => $palta->id, 'nombre' => 'Palta', 'descripcion' => 'Malus sylvestris']);
        $apricot= Especie::create(['tenant_id' => $tid, 'familia_id' => $carozos->id, 'nombre' => 'Albaricoque', 'descripcion' => 'Prunus armeniaca']);
        $caquis= Especie::create(['tenant_id' => $tid, 'familia_id' => $ebanaceas->id, 'nombre' => 'Caquis', 'descripcion' => 'Diospyros kaki']);
        $arandanos= Especie::create(['tenant_id' => $tid, 'familia_id' => $berries->id, 'nombre' => 'Arándanos', 'descripcion' => 'Vaccinium corymbosum']);
        $clementinas= Especie::create(['tenant_id' => $tid, 'familia_id' => $citricos->id, 'nombre' => 'Clementinas', 'descripcion' => 'Citrus clementina']);
        $granada= Especie::create(['tenant_id' => $tid, 'familia_id' => $litraceas->id, 'nombre' => 'Granada', 'descripcion' => 'Citrus grandis']);
        $grapes= Especie::create(['tenant_id' => $tid, 'familia_id' =>$vitaceas->id, 'nombre' => 'Uvas', 'descripcion' => 'Vitis vinifera']);


        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Tote', 'peso_bin_kg' => 220,'especie_id' => $cherries->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Smart Picker', 'peso_bin_kg' => 228, 'especie_id' => $cherries->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Balde', 'peso_bin_kg' => 220,'especie_id' => $cherries->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Tote', 'peso_bin_kg' => 435,'especie_id' => $peaches->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Capachada', 'peso_bin_kg' => 435,'especie_id' => $peaches->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Granel', 'peso_bin_kg' => 435,'especie_id' => $peaches->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Tote', 'peso_bin_kg' => 435,'especie_id' => $nectarines->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Capachada', 'peso_bin_kg' => 435,'especie_id' => $nectarines->id]);
        ContenedorCosecha::create(['tenant_id' => $tid, 'nombre' => 'Granel', 'peso_bin_kg' => 435,'especie_id' => $nectarines->id]);

        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Fuji', 'descripcion' => 'Dulce y crujiente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Granny Smith', 'descripcion' => 'Color verde, sabor ácido']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Royal Gala', 'descripcion' => 'Color rojo anaranjado, dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Scarlett', 'descripcion' => 'Color rojo intenso, textura firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Pink Lady', 'descripcion' => 'Sabor equilibrado, crujiente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Cripps Pink', 'descripcion' => 'Variedad equilibrada, piel rosada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Red Chief', 'descripcion' => 'Color rojo brillante, forma alargada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Buckeye', 'descripcion' => 'Color rojo intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Gala Premium', 'descripcion' => 'Calidad superior, dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Galaxy', 'descripcion' => 'Color rojo muy atractivo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Fuji Raku Raku', 'descripcion' => 'Versión mejorada de Fuji']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Red Delicious', 'descripcion' => 'Clásica, rojo oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Royal Gala Premium', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Brookfield', 'descripcion' => 'Color rojo uniforme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Granny Smith Spur', 'descripcion' => 'Variedad compacta']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Ambrosia', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Rossy Glo', 'descripcion' => 'Color rosado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Kanzi', 'descripcion' => 'Equilibrada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Gala', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Red King Oregon', 'descripcion' => 'Rojo intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Rossy', 'descripcion' => 'Color rosado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Galaxis', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Pacific Gala', 'descripcion' => 'Calidad premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Braeburn', 'descripcion' => 'Sabor intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Ultra Red Gala', 'descripcion' => 'Rojo intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Rosy Glow', 'descripcion' => 'Color atractivo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Blackjon', 'descripcion' => 'Color oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Early Red One', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Starkrimson Delicious', 'descripcion' => 'Rojo oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Top Red', 'descripcion' => 'Rojo intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Raku Raku', 'descripcion' => 'Versión mejorada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Gala Buckeye', 'descripcion' => 'Color intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Honey Crips', 'descripcion' => 'Muy crujiente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Sun Fuji', 'descripcion' => 'Color brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Ginger Gold', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Fuji Royal', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Evelina', 'descripcion' => 'Crujiente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Fuji Fubrax', 'descripcion' => 'Color intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apples->id, 'nombre' => 'Cripps Pink CPVR Pink Lady', 'descripcion' => 'Premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Abate Fetel', 'descripcion' => 'Forma alargada, dulce y jugosa']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Winter Nelis', 'descripcion' => 'Piel rústica, sabor mantecoso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Packhams Triumph', 'descripcion' => 'Piel rugosa, muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Forelle', 'descripcion' => 'Pequeña, amarillo con manchas rojas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Salvador Izquierdo', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Asiatica', 'descripcion' => 'Crujiente y jugosa']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Favorita', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Summer Bartlett', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'D Anjou', 'descripcion' => 'Firme y duradera']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Sweet Sensation', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Beurre Bosc', 'descripcion' => 'Piel rústica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Coscia', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Beurre D Anjou', 'descripcion' => 'Piel verde']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Red Bartlett', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Singo', 'descripcion' => 'Variedad asiática']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Golden', 'descripcion' => 'Color dorado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Flamingo', 'descripcion' => 'Color especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Berry Bosc', 'descripcion' => 'Piel rústica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Brown', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Carmen', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $pera->id, 'nombre' => 'Shinko', 'descripcion' => 'Variedad asiática']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $kiwi->id, 'nombre' => 'Hayward', 'descripcion' => 'Pulpa verde y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $kiwi->id, 'nombre' => 'Green Light', 'descripcion' => 'Pulpa verde']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $kiwi->id, 'nombre' => 'Summer Kiwi', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $kiwi->id, 'nombre' => 'Jintao', 'descripcion' => 'Pulpa amarilla']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $membrillo->id, 'nombre' => 'Champion', 'descripcion' => 'Fragante, ideal para mermeladas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Red Globe', 'descripcion' => 'Grano grande, sabor dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Thompson Seedless', 'descripcion' => 'Sin semillas, muy popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Creemsom Seedles', 'descripcion' => 'Color rojo, sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Crimson Seedless', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Arra 15', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sugraone', 'descripcion' => 'Clásica sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sweet Celebration', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Timco', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sweeties', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'White Seedless', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Arra 29', 'descripcion' => 'Sabor intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Moscatel', 'descripcion' => 'Muy aromática']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Autumn Royal', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Allison', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Escarlota', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Timpson', 'descripcion' => 'Blanca dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sweet Globe', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sheegene-2', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Arra', 'descripcion' => 'Serie Arra']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Crimson', 'descripcion' => 'Clásica roja']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Export', 'descripcion' => 'Calidad exportación']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sheegene 20', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Ivor', 'descripcion' => 'Blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Autum Crisp', 'descripcion' => 'Crujiente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sheegene 13', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'IFG Three', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sugrasixteen', 'descripcion' => 'Variedad Sugra']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Melody', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sweet Favors', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Jack’s Salute', 'descripcion' => 'Muy popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Cotton Candy', 'descripcion' => 'Sabor especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Candy Hearts', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sable Seedlees', 'descripcion' => 'Sabor intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sheegene 12', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $grapes->id, 'nombre' => 'Sheegene', 'descripcion' => 'Serie Sheegene']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Bing', 'descripcion' => 'Rojo oscuro, dulce y firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Lapins', 'descripcion' => 'Grande, resistente al partido']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Kordia', 'descripcion' => 'Forma acorazonada, muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Rainier', 'descripcion' => 'Color amarillo, dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sweet Heart', 'descripcion' => 'Tardía, muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Regina', 'descripcion' => 'Muy firme y tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Santina', 'descripcion' => 'Precoz, forma de corazón']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Van', 'descripcion' => 'Rojo brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Dawn', 'descripcion' => 'Precoz y de gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Glen Red', 'descripcion' => 'Color rojo brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Cristalina', 'descripcion' => 'Muy dulce y resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Early Burlat', 'descripcion' => 'Muy precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Brooks', 'descripcion' => 'Calibre grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Stella', 'descripcion' => 'Dulce y productiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Chelan', 'descripcion' => 'Muy precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Skeena', 'descripcion' => 'Firme y tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Celeste', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Lambert', 'descripcion' => 'Rojo oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Summit', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Black Tartarian', 'descripcion' => 'Rojo muy oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sylvia', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Early Bing', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Newstar', 'descripcion' => 'Auto-fértil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Staccato', 'descripcion' => 'Muy tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Samson', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Lee', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Tulare', 'descripcion' => 'Excelente sabor']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Lihn', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Hazel', 'descripcion' => 'Calibre medio']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Frisco', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Lynn', 'descripcion' => 'Productiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sequoia', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Mini Royale', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'C-15', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Rainier', 'descripcion' => 'Color amarillo/rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Bailey', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Capitana', 'descripcion' => 'Muy productiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Edie', 'descripcion' => 'Excelente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'C-14', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sam', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Alex', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sue', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Corazon De Paloma', 'descripcion' => 'Forma acorazonada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sunburst', 'descripcion' => 'Grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Schneider', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Helen', 'descripcion' => 'Excelente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Synphoni', 'descripcion' => 'Excelente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sentennial', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sweet Aryana', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Red Pacific', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Nimba', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Areko', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Sweet Lorenz', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Pacific Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Somerset', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Glen Rock', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Tip Top', 'descripcion' => 'Excelente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Garnet', 'descripcion' => 'Color oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Tioga', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Cherry Treat', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Ivu - 105', 'descripcion' => 'Técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Ivu - 115', 'descripcion' => 'Técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Ivu - 104', 'descripcion' => 'Técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-Eight', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-Ten', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-Five', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-One', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-Six', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IFG Cher-Two', 'descripcion' => 'Variedad IFG']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Nimba Star', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'IVU-104 / MEDA R BULL', 'descripcion' => 'Combinada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Dawn 2', 'descripcion' => 'Mejorada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Superior', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Royal Dawn*', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $cherries->id, 'nombre' => 'Coral', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Fire', 'descripcion' => 'Pulpa amarilla, muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Glenn', 'descripcion' => 'Color rojo intenso, jugosa']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Ruby Diamond', 'descripcion' => 'Brillante y firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Early Glo', 'descripcion' => 'Maduración temprana']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Big Boy', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Zee Fire', 'descripcion' => 'Dulce y brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Glo', 'descripcion' => 'Color rojo intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Star', 'descripcion' => 'Pulpa blanca, dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Mist', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Snow', 'descripcion' => 'Blanca y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Red', 'descripcion' => 'Maduración tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Snow', 'descripcion' => 'Pulpa blanca tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Bright Pearl', 'descripcion' => 'Muy brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Fire Pearl', 'descripcion' => 'Intensa y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Super Queen', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Venus', 'descripcion' => 'Versátil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Honey Blaze', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sunrise', 'descripcion' => 'Maduración temprana']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Magique', 'descripcion' => 'Gran sabor']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Isi White', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Candy White', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Spring Red', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Queen', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'White Angel', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Perlicius V', 'descripcion' => 'Variedad mejorada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Summer Bright', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Peace White', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Orion', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Pearl', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Majestic Pearl', 'descripcion' => 'Muy grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectar Crest', 'descripcion' => 'Excelente aroma']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Zee Glo', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Grand Bright', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'King White', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Giant Pearl', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Pride', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Giant', 'descripcion' => 'Grande y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Big Pearl', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Super August', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Super Manon', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectajewel', 'descripcion' => 'Calidad premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'May Pearl', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Andesneccuatro', 'descripcion' => 'Variedad adaptada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Diamond', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Summer Diamond', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'September Bright', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-308', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Summer Red', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Jim', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Andesnecuno', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectariane', 'descripcion' => 'Versátil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Pearl', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Andesnectres', 'descripcion' => 'Adaptada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Gartario', 'descripcion' => 'Versátil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Fiesta', 'descripcion' => 'Festiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Fire Sweet', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'September Red', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Jay', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-678', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Summer Brite', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Grand', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'July Pearl', 'descripcion' => 'Maduración media']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'River Pearl', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'River Sweet', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Kay Diamond', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-483', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-577', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-360', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Kiss', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Bite', 'descripcion' => 'Pequeña y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Spring Bright', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Bright', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Antonia', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Dream', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Flavor Ale', 'descripcion' => 'Sabor especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Royal Glo', 'descripcion' => 'Color intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-100', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Snow Sweet', 'descripcion' => 'Blanca y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Super Giant', 'descripcion' => 'Muy grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'August Glo', 'descripcion' => 'Color intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Queen', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'July Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Rock Pearl', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Gaitairo', 'descripcion' => 'Versátil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Andesnecdos', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Magnum Red', 'descripcion' => 'Grande y roja']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Ne 300', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Extreme 303', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Cakedelice', 'descripcion' => 'Sabor dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Pro 712', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectarperle', 'descripcion' => 'Color perlado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectarperle-Regalin', 'descripcion' => 'Variedad premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectariane-Regalin', 'descripcion' => 'Variedad premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectarjewel-Regalin', 'descripcion' => 'Variedad premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Garofa', 'descripcion' => 'Versátil']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE-891', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Lizama', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Summer Fire', 'descripcion' => 'Brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Azurite', 'descripcion' => 'Color especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE 269', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Andesnecseis', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Just Sweet', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Sweet Pearl', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'NE 848', 'descripcion' => 'Técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Red Dream', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'September Queen', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Arctic Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Flamekist', 'descripcion' => 'Color intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $nectarines->id, 'nombre' => 'Nectacrest', 'descripcion' => 'Aromática']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'White Lady', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Kurakata', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Andross', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Carson', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Elegant Lady', 'descripcion' => 'Excelente sabor']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Ross Peach', 'descripcion' => 'Variedad productiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Rock Lady', 'descripcion' => 'Firme y duradera']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'June Lady', 'descripcion' => 'Maduración temprana']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Toscana', 'descripcion' => 'Variedad europea']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Loudel', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Snow King', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Bowen', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Doctor Davis', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'September Sun', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'September Snow', 'descripcion' => 'Pulpa blanca tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Evertz', 'descripcion' => 'Variedad firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Sweet September', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Zee Lady', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Desert Gold', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Rosario Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Corona', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Spring Snow', 'descripcion' => 'Precoz blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Younge Beauty', 'descripcion' => 'Muy atractiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Early Beauty', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'August Pride', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Kakama', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Klampt', 'descripcion' => 'Resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Sweet Snow', 'descripcion' => 'Blanca y dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Ivory Princess', 'descripcion' => 'Blanca premium']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Autumn Zee', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'DU-600', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Pomona', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Candy Sweet', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Sierra Snow', 'descripcion' => 'Blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Kaweah', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Robin Neil', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Royal Glory', 'descripcion' => 'Calidad superior']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Spring Beauty', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Brittney Lane', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Omarcito', 'descripcion' => 'Variedad adaptada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Blanquillo', 'descripcion' => 'Pulpa blanca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $peaches->id, 'nombre' => 'Spring Lady', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Aurora', 'descripcion' => 'Color brillante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Ambar', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Amber', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Zafiro', 'descripcion' => 'Color azulado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Angeleno', 'descripcion' => 'Muy popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Friar', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Kat', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Larry Anne', 'descripcion' => 'Calibre grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sapphire', 'descripcion' => 'Color azul']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Fortune', 'descripcion' => 'Excelente sabor']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Dagen', 'descripcion' => 'Variedad firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Dapple Dandy', 'descripcion' => 'Piel jaspeada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Dapple Delight', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Blue Gusto', 'descripcion' => 'Sabor intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Roysum', 'descripcion' => 'Grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sweet Mary', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Happy Giant', 'descripcion' => 'Calibre gigante']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Red Heart', 'descripcion' => 'Pulpa roja']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Red Granade', 'descripcion' => 'Piel roja']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Majesty', 'descripcion' => 'Piel negra']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Red Lyon', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Ci 206', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Howard Sun', 'descripcion' => 'Calibre grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Crimson Fall', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'September Yummy', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Lemon', 'descripcion' => 'Color amarillo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Autumn Pride', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Flavor Rich', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Laetittia', 'descripcion' => 'Excelente calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Blue Giant', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Yummy Giant', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Big Kat', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Candy Red', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Deep Blue', 'descripcion' => 'Color azul intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Owen T', 'descripcion' => 'Variedad resistente']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Pink Delight', 'descripcion' => 'Color rosado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Royal Zee', 'descripcion' => 'Excelente sabor']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Ambra', 'descripcion' => 'Color ambar']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Heart', 'descripcion' => 'Forma de corazón']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Crimson Dawn', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Flavor Sweet', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Big Fusion', 'descripcion' => 'Muy grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sweet Garnet I', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Flavor Fall', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Flavor Fusion', 'descripcion' => 'Equilibrada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Festival Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Crimson Kat', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Emerald Candy', 'descripcion' => 'Pulpa clara']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Emerald Sweet', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Candy Stripe', 'descripcion' => 'Jaspeada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Angelino', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Back Splendor', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Autumn Giant', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Ebony Sweet', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Majestic', 'descripcion' => 'Grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Summer Breeze', 'descripcion' => 'Muy fresca']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Red Phoenix', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'August Yummy', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Hiromi Red', 'descripcion' => 'Color rojo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sweet Lady', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Ruby Kat', 'descripcion' => 'Firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Fallete', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Diamond', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Black Sweet', 'descripcion' => 'Dulce y firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Varias', 'descripcion' => 'Mezcla de variedades']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Fall Fiesta', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Silver Red', 'descripcion' => 'Color especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Injerto', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sugar Plum', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Giant Phoenix', 'descripcion' => 'Muy grande']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'A13-16', 'descripcion' => 'Variedad técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Honey Punch', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'African Delight', 'descripcion' => 'Color oscuro']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Emerald Dream', 'descripcion' => 'Pulpa clara']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Emerald King', 'descripcion' => 'Muy firme']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Candy Bite', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Flavor Blast', 'descripcion' => 'Sabor intenso']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sweet Pixies', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Ebony Rose', 'descripcion' => 'Piel oscura']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sunset Delight', 'descripcion' => 'Color atractivo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sweet Pekeetah', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Tulare Giant', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Sun Gold', 'descripcion' => 'Color dorado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Leticia', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'K-2-61', 'descripcion' => 'Técnica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Candy Plum', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Red Phoenix Giant', 'descripcion' => 'Gran calibre']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $plums->id, 'nombre' => 'Turtle Egg', 'descripcion' => 'Forma especial']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apricot->id, 'nombre' => 'Dina', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apricot->id, 'nombre' => 'Patterson', 'descripcion' => 'Muy productiva']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apricot->id, 'nombre' => 'Castel Bitte', 'descripcion' => 'Dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $apricot->id, 'nombre' => 'Modesto', 'descripcion' => 'Adaptada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $limon->id, 'nombre' => 'Eureka', 'descripcion' => 'El más común']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $limon->id, 'nombre' => 'Pink', 'descripcion' => 'Color rosado']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Navel', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Fukumoto', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Ley Ley', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Valencia Midnight', 'descripcion' => 'Jugosa']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Newhall', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Lane Late', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Valencia', 'descripcion' => 'La más usada para jugo']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $naranja->id, 'nombre' => 'Navel Late', 'descripcion' => 'Tardía']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Suki', 'descripcion' => 'Sabor dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'W. Murcott', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Murcott', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Tango', 'descripcion' => 'Sin semillas']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Clementina', 'descripcion' => 'Clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Clemenluz', 'descripcion' => 'Precoz']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Orogrande', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $clementinas->id, 'nombre' => 'Clemenule', 'descripcion' => 'La más popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $granada->id, 'nombre' => 'Wonderful', 'descripcion' => 'La más popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $caquis->id, 'nombre' => 'Giro', 'descripcion' => 'Variedad clásica']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $caquis->id, 'nombre' => 'Fuyu', 'descripcion' => 'El más popular']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $caquis->id, 'nombre' => 'Hachiya', 'descripcion' => 'Forma alargada']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $arandanos->id, 'nombre' => 'Rocio', 'descripcion' => 'Muy dulce']);
        Variedad::create(['tenant_id' => $tid, 'especie_id' => $palta->id, 'nombre' => 'Hass', 'descripcion' => 'La más popular']);

        //Centros de Costo
        // Fundo Los Niches
        CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NIFU021', 'nombre' => 'Fundo Los Niches', 'activo' => true,'agrupador'=>'NR4']);
        $NICE027=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NICE027', 'nombre' => 'Los Niches - Cuartel 2 y 4 - Cereza - Regina-Kordia-Skeena', 'activo' => true,'agrupador'=>'NR4']);
        $NICE028=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NICE028', 'nombre' => 'Los Niches - Cuartel 1, 3 y 5 - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR4']);
        $NIMT048=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NIMT048', 'nombre' => 'Los Niches - Tractor Lovol', 'activo' => true,'agrupador'=>'NR4']);
        $NIMN049=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NIMN049', 'nombre' => 'Los Niches - Nebulizadora Jacto 1500', 'activo' => true,'agrupador'=>'NR4']);
        $NIMH050=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NIMH050', 'nombre' => 'Los Niches - Maquina Herbicida Rautop', 'activo' => true,'agrupador'=>'NR4']);
        $NICE067=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'NICE067', 'nombre' => 'Los Niches - Cuartel 1, 3 y 5 - Cereza - Santina', 'activo' => true,'agrupador'=>'NR4']);

        // Fundo La Esperanza
        CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESFU022', 'nombre' => 'Fundo La Esperanza', 'activo' => true,'agrupador'=>'NR2']);
        $ESMT001=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESMT001', 'nombre' => 'La Esperanza - Tractor Massey Ferguson 4275', 'activo' => true,'agrupador'=>'NR2']);
        $ESMT002=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESMT002', 'nombre' => 'La Esperanza - Tractor Massey Ferguson 4276', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE002=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE002', 'nombre' => 'La Esperanza - Cuartel 2 (1) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR2']);
        $ESMT003=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESMT003', 'nombre' => 'La Esperanza - Tractor Europard 504', 'activo' => true,'agrupador'=>'NR2']);
        $ESMT004=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESMT004', 'nombre' => 'La Esperanza - Tractor Frutero Same', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE003=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE003', 'nombre' => 'La Esperanza - Cuartel 3.1 (1) - Cereza - Royal Dawn-Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE005=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE005', 'nombre' => 'La Esperanza - Cuartel 4.1 (1) - Cereza - Rainier-Santina-Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE008=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE008', 'nombre' => 'La Esperanza - Cuartel 5.1 (1) - Cereza - Royal Dawn-Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE010=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE010', 'nombre' => 'La Esperanza - Cuartel 1 (2) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE011=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE011', 'nombre' => 'La Esperanza - Cuartel 2 (2) - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE015=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE015', 'nombre' => 'La Esperanza - Cuartel 6 (2) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE016=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE016', 'nombre' => 'La Esperanza - Cuartel 1 (3) - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE018=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE018', 'nombre' => 'La Esperanza - Cuartel 3 (3) - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI062=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI062', 'nombre' => 'La Esperanza - Cuartel 3 (2) - Tulare-Miur Beuty', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI063=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI063', 'nombre' => 'La Esperanza - Cuartel 4 (2) - Ciruela- Silver red-Red Phoenix', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE077=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE077', 'nombre' => 'La Esperanza - Cuartel 2 y 3: Rainier, Lapins', 'activo' => true,'agrupador'=>'NR2']);
        $ESCE059=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCE059', 'nombre' => 'La Esperanza - Cuartel 2 (3) - Cereza - Rtyoga', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI060=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI060', 'nombre' => 'La Esperanza - Cuartel 4 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI061=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI061', 'nombre' => 'La Esperanza - Cuartel 6 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI073=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI073', 'nombre' => 'La Esperanza - Cuartel 1 y 4, Ciruela Sweet mery, Candy red, Red Phoenix', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI074=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI074', 'nombre' => 'La Esperanza - Ciruelo Candy red', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI075=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI075', 'nombre' => 'La Esperanza - Cuartel 5, Ciruelo Sweet Mery, Blue Gusto', 'activo' => true,'agrupador'=>'NR2']);
        $ESCI076=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'ESCI076', 'nombre' => 'La Esperanza - Ciruelo Emeral Red', 'activo' => true,'agrupador'=>'NR2']);
        // Fundo Paine
        $PAFU023=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAFU023', 'nombre' => 'Fundo Paine', 'activo' => true,'agrupador'=>'NR6']);
        $PACE029=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE029', 'nombre' => 'Paine (Campo 1) - Cuartel 1.(1) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR6']);
        $PACE030=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE030', 'nombre' => 'Paine (Campo 1) - Cuartel 2 y 3 (1) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR6']);
        $PACE031=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE031', 'nombre' => 'Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR6']);
        $PACE032=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE032', 'nombre' => 'Paine (Campo 1) - Cuartel 5 (1) - Cererza - Lapins-Santina', 'activo' => true,'agrupador'=>'NR6']);
        $PACE034=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE034', 'nombre' => 'Paine (Campo 1) - Cuartel 1 y 2 (2) - Nectarin - White Angel', 'activo' => true,'agrupador'=>'NR6']);
        $PACE035=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE035', 'nombre' => 'Paine (Campo 1) - Cuartel 3 y 4 (2) - Nectarin - Andesneccuatro', 'activo' => true,'agrupador'=>'NR6']);
        $PACE036=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE036', 'nombre' => 'Paine (Campo 1) - Cuartel 5 (2) - Nectarin - Sweet Giant', 'activo' => true,'agrupador'=>'NR6']);
        $PACE037=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE037', 'nombre' => 'Paine (Campo 1) - Cuartel 6 (2) - Nectarin - Sweet Giant', 'activo' => true,'agrupador'=>'NR6']);
        $PACI038=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACI038', 'nombre' => 'Paine (Campo 2) - Cuartel 1, 2, 3 y 4 (3) - Ciruela - Varias', 'activo' => true,'agrupador'=>'NR6']);
        $PACE039=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE039', 'nombre' => 'Paine (Campo 2) - Cuartel 5 (3) - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR6']);
        $PAMT016=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMT016', 'nombre' => 'Paine - Tractor Same 01', 'activo' => true,'agrupador'=>'NR6']);
        $PAMP018=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMP018', 'nombre' => 'Paine - Pulverizadora Jacto 2000 Lts', 'activo' => true,'agrupador'=>'NR6']);
        $PAMH019=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMH019', 'nombre' => 'Paine - Herbocida Jacto 400 Lts', 'activo' => true,'agrupador'=>'NR6']);
        $PAMT051=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMT051', 'nombre' => 'Paine - Tractor Landini Rex', 'activo' => true,'agrupador'=>'NR6']);
        $PAMR056=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMR056', 'nombre' => 'Paine - Equipo de Riego', 'activo' => true,'agrupador'=>'NR6']);
        $PAMR061=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PAMR061', 'nombre' => 'Paine - Bomba de Riego', 'activo' => true,'agrupador'=>'NR6']);
        $PACE068=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'PACE068', 'nombre' => 'Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina', 'activo' => true,'agrupador'=>'NR6']);

        // Fundo El Carmen
        $CRFU025=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CRFU025', 'nombre' => 'Fundo El Carmen GR', 'activo' => true,'agrupador'=>'NR1']);
        $CRNE069=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CRNE069', 'nombre' => 'El Carmen - Cuartel 1 - Nectarin - White Angel', 'activo' => true,'agrupador'=>'NR1']);
        $CRNE070=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CRNE070', 'nombre' => 'El Carmen - Cuartel 2 - Nectarin - Nectariane', 'activo' => true,'agrupador'=>'NR1']);
        $CRNE071=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CRNE071', 'nombre' => 'El Carmen - Cuartel 3 - Nectarin - Nectarjewel', 'activo' => true,'agrupador'=>'NR1']);
        $CRCI072=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CRCI072', 'nombre' => 'El Carmen 2 - Ciruela', 'activo' => true,'agrupador'=>'NR1']);

        // Fundo Santa Isabel
        $SIFU024=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIFU024', 'nombre' => 'Fundo Santa Isabel', 'activo' => true,'agrupador'=>'NR8']);
        $SINE100=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE100', 'nombre' => 'Santa Isabel - Nectarin Artic Star', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU101=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU101', 'nombre' => 'Santa Isabel - Durazno White Lady', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU102=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU102', 'nombre' => 'Santa Isabel - Durazno Carson', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU103=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU103', 'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU104=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU104', 'nombre' => 'Santa Isabel - Durazno  Andross', 'activo' => true,'agrupador'=>'NR8']);
        $SINE105=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE105', 'nombre' => 'Santa Isabel - Nectarin Super Manon', 'activo' => true,'agrupador'=>'NR8']);
        $SINE106=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE106', 'nombre' => 'Santa Isabel - Nectarin Super Manon', 'activo' => true,'agrupador'=>'NR8']);
        $SINE107=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE107', 'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'activo' => true,'agrupador'=>'NR8']);
        $SINE108=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE108', 'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'activo' => true,'agrupador'=>'NR8']);
        $SINE109=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE109', 'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'activo' => true,'agrupador'=>'NR8']);
        $SINE110=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SINE110', 'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU111=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU111', 'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'activo' => true,'agrupador'=>'NR8']);
        $SIDU112=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIDU112', 'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'activo' => true,'agrupador'=>'NR8']);
        $SIMT011=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIMT011', 'nombre' => 'Santa Isabel - Tractor Landini REXDT85F', 'activo' => true,'agrupador'=>'NR8']);
        $SIMT012=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SIMT012', 'nombre' => 'Santa Isabel - Tractor Landini R75F', 'activo' => true,'agrupador'=>'NR8']);

        // Fundo San Andrés
        $SAFU026=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SAFU026', 'nombre' => 'Fundo San Andrés', 'activo' => true,'agrupador'=>'NR9']);
        $SADU113=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SADU113', 'nombre' => 'San Andrés - Durazno Zee Lady', 'activo' => true,'agrupador'=>'NR9']);
        $SADU114=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SADU114', 'nombre' => 'San Andrés - Durazno Ivory Princess', 'activo' => true,'agrupador'=>'NR9']);
        $SANE115=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SANE115', 'nombre' => 'San Andrés - Nectarin Bright Pearl', 'activo' => true,'agrupador'=>'NR9']);
        $SADU116=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SADU116', 'nombre' => 'San Andrés - Durazno September Snow', 'activo' => true,'agrupador'=>'NR9']);
        $SANE117=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SANE117', 'nombre' => 'San Andrés - Nectarin Agust Pearl', 'activo' => true,'agrupador'=>'NR9']);
        $SANE118=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SANE118', 'nombre' => 'San Andrés - Nectarin Giant Pearl', 'activo' => true,'agrupador'=>'NR9']);
        $SAME119=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SAME119', 'nombre' => 'San Andrés - Membrillo Champion', 'activo' => true,'agrupador'=>'NR9']);
        $SAME120=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'SAME120', 'nombre' => 'San Andrés - Membrillo Champion', 'activo' => true,'agrupador'=>'NR9']);

        //Fundo Las Cabras
        $CACE047=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE047', 'nombre' => 'Las Cabras - Cuartel 4.1 - Cereza - Lapins', 'activo' => true,'agrupador'=>'NR10']);
        $CACE049=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE049', 'nombre' => 'Las Cabras - Cuartel 6 - Cereza - Reinier-Lapins', 'activo' => true,'agrupador'=>'NR10']);
        $CACE053=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE053', 'nombre' => 'Las Cabras - Cuartel 4.4 - Cereza - Sweet Aryana', 'activo' => true,'agrupador'=>'NR10']);
        $CACE054=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE054', 'nombre' => 'Las Cabras - Cuartel 4.5 - Cereza - Red Pacific', 'activo' => true,'agrupador'=>'NR10']);
        $CACE055=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE055', 'nombre' => 'Las Cabras - Cuartel 2.3 - Cereza - Nimba', 'activo' => true,'agrupador'=>'NR10']);
        $CACE056=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE056', 'nombre' => 'Las Cabras - Cuartel 2.4 - Cereza - Red Pacific', 'activo' => true,'agrupador'=>'NR10']);
        $CACE066=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACE066', 'nombre' => 'Las Cabras - Cuartel 5.1 - Cereza - Cherry Treat-Lapins', 'activo' => true,'agrupador'=>'NR10']);
        $CACI046=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACI046', 'nombre' => 'Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix', 'activo' => true,'agrupador'=>'NR10']);
        $CACI058=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CACI058', 'nombre' => 'Las Cabras - Cuartel 1.2 - Ciruela - Injerto', 'activo' => true,'agrupador'=>'NR10']);
        $CAFU029=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CAFU029', 'nombre' => 'Fundo Las Cabras', 'activo' => true,'agrupador'=>'NR10']);
        $CANE044=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CANE044', 'nombre' => 'Las Cabras - Cuartel 3.1 - Nectarin - Majestic Pearl', 'activo' => true,'agrupador'=>'NR10']);
        $CANE045=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CANE045', 'nombre' => 'Las Cabras - Cuartel 3.2 - Nectarin - Andesneccuatro', 'activo' => true,'agrupador'=>'NR10']);
        $CANE046=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CANE046', 'nombre' => 'Las Cabras - Cuartel 3.3 - Nectarin - Rock Pearl', 'activo' => true,'agrupador'=>'NR10']);
        $CANE064=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CANE064', 'nombre' => 'Las Cabras - Cuartel 1 - Nectarin - Snow Sweet', 'activo' => true,'agrupador'=>'NR10']);
        $CANE065=CentroCosto::create(['tenant_id' => $tid, 'codigo' => 'CANE065', 'nombre' => 'Las Cabras - Cuartel 1 - Nectarin - Majestic Pearl', 'activo' => true,'agrupador'=>'NR10']);

                // El Carmen
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'El Carmen 2 - Ciruela', 'especie_id' => $plums->id, 'centro_costo_id' => $CRCI072->id,'superficie_hectareas'=>5,'ano_plantacion'=>2024]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'El Carmen - Cuartel 1 -Nectarin - White Angel', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CRNE069->id,'superficie_hectareas'=>1.8,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'El Carmen - Cuartel 2 - Nectarin - Nectariane', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CRNE070->id,'superficie_hectareas'=>2,'ano_plantacion'=>2018]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'El Carmen - Cuartel 3 - Nectarin - Nectarjewel', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CRNE071->id,'superficie_hectareas'=>1.8,'ano_plantacion'=>2018]);

// La Esperanza
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 2 (1) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE002->id,'superficie_hectareas'=>04.88,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 3.1 (1) - Cereza - Royal Dawn-Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE003->id,'superficie_hectareas'=>4.87,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 4.1 (1) - Cereza - Rainier-Santina-Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE005->id,'superficie_hectareas'=>10.07,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 5.1 (1) - Cereza - Royal Dawn-Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE008->id,'superficie_hectareas'=>4.15,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 1 (2) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE010->id,'superficie_hectareas'=>8.21,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 2 (2) - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE011->id,'superficie_hectareas'=>7.92,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 6 (2) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE015->id,'superficie_hectareas'=>6.84,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 1 (3) - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE016->id,'superficie_hectareas'=>3.47,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 3 (3) - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE018->id,'superficie_hectareas'=>3.65,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 2 (3) - Cereza - Rtyoga', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE059->id,'superficie_hectareas'=>3.36,'ano_plantacion'=>2022]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 2 y 3: Rainier, Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $ESCE077->id,'superficie_hectareas'=>7.57,'ano_plantacion'=>2025]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 4 (3) - Ciruelo Turtle egg-RedPhoenix-Candy Red', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI060->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2023]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 6 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI061->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2023]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 3 (2) - Tulare-Miur Beuty', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI062->id,'superficie_hectareas'=>7.77,'ano_plantacion'=>2023]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 4 (2) - Ciruela- Silver red-Red Phoenix', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI063->id,'superficie_hectareas'=>6.44,'ano_plantacion'=>2023]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 1 y 4, Ciruela Sweet mery, Candy red, Red Phoenix', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI073->id,'superficie_hectareas'=>7.56,'ano_plantacion'=>2025]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Ciruelo Candy red', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI074->id,'superficie_hectareas'=>5.55,'ano_plantacion'=>2025]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Cuartel 5, Ciruelo Sweet Mery, Blue Gusto', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI075->id,'superficie_hectareas'=>3.33,'ano_plantacion'=>2025]);
Cuartel::create(['tenant_id' => $tid, 'nombre' => 'La Esperanza - Ciruelo Emeral Red', 'especie_id' => $plums->id, 'centro_costo_id' => $ESCI076->id,'superficie_hectareas'=>3,'ano_plantacion'=>2025]);

//Las Cabras
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 4.1 - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE047->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 6 - Cereza - Reinier-Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE049->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 4.4 - Cereza - Sweet Aryana', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE053->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 4.5 - Cereza - Red Pacific', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE054->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 2.3 - Cereza - Nimba', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE055->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 2.4 - Cereza - Red Pacific', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE056->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 5.1 - Cereza - Cherry Treat-Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $CACE066->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix', 'especie_id' => $plums->id, 'centro_costo_id' => $CACI046->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 1.2 - Ciruela - Injerto', 'especie_id' => $plums->id, 'centro_costo_id' => $CACI058->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Fundo Las Cabras', 'especie_id' => null, 'centro_costo_id' => $CAFU029->id,'superficie_hectareas'=>0,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 3.1 - Nectarin - Majestic Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CANE044->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 3.2 - Nectarin - Andesneccuatro', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CANE045->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 3.3 - Nectarin - Rock Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CANE046->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 1 - Nectarin - Snow Sweet', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CANE064->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Las Cabras - Cuartel 1 - Nectarin - Majestic Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $CANE065->id,'superficie_hectareas'=>3.5,'ano_plantacion'=>2020]);


// Los Niches
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Los Niches - Cuartel 2 y 4 - Cereza - Regina-Kordia-Skeena', 'especie_id' => $cherries->id, 'centro_costo_id' => $NICE027->id,'superficie_hectareas'=>3.63,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Los Niches - Cuartel 1, 3 y 5 - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $NICE028->id,'superficie_hectareas'=>5.78,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Los Niches - Cuartel 1, 3 y 5 - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $NICE067->id,'superficie_hectareas'=>10.07,'ano_plantacion'=>2022]);

// Paine
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 1.(1) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE029->id,'superficie_hectareas'=>1.71,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 2 y 3 (1) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE030->id,'superficie_hectareas'=>2.68,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE031->id,'superficie_hectareas'=>1.55,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 5 (1) - Cererza - Lapins-Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE032->id,'superficie_hectareas'=>1.42,'ano_plantacion'=>2014]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 1 y2 (2) - Nectarin - White Angel', 'especie_id' => $nectarines->id, 'centro_costo_id' => $PACE034->id,'superficie_hectareas'=>1.2,'ano_plantacion'=>2018]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 3 y 4 (2) - Nectarin - Andesneccuatro', 'especie_id' => $nectarines->id, 'centro_costo_id' => $PACE035->id,'superficie_hectareas'=>5.38,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 5 (2) - Nectarin - Sweet Giant', 'especie_id' => $nectarines->id, 'centro_costo_id' => $PACE036->id,'superficie_hectareas'=>1.88,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 6 (2) - Nectarin - Sweet Giant', 'especie_id' => $nectarines->id, 'centro_costo_id' => $PACE037->id,'superficie_hectareas'=>1.59,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 2) - Cuartel 5 (3) - Cereza - Lapins', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE039->id,'superficie_hectareas'=>2.53,'ano_plantacion'=>2016]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina', 'especie_id' => $cherries->id, 'centro_costo_id' => $PACE068->id,'superficie_hectareas'=>1.59,'ano_plantacion'=>2022]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Paine (Campo 2) - Cuartel 1, 2, 3 y 4 (3) - Ciruela - Varias', 'especie_id' => $plums->id, 'centro_costo_id' => $PACI038->id,'superficie_hectareas'=>8.76,'ano_plantacion'=>2020]);

// San Andrés
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Durazno Zee Lady', 'especie_id' => $peaches->id, 'centro_costo_id' => $SADU113->id,'superficie_hectareas'=>0.43,'ano_plantacion'=>2017]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Durazno Ivory Princess', 'especie_id' => $peaches->id, 'centro_costo_id' => $SADU114->id,'superficie_hectareas'=>2.07,'ano_plantacion'=>2017]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Durazno September Snow', 'especie_id' => $peaches->id, 'centro_costo_id' => $SADU116->id,'superficie_hectareas'=>1.14,'ano_plantacion'=>2017]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Membrillo Champion', 'especie_id' => $membrillo->id, 'centro_costo_id' => $SAME119->id,'superficie_hectareas'=>2.61,'ano_plantacion'=>2016]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Membrillo Champion', 'especie_id' => $membrillo->id, 'centro_costo_id' => $SAME120->id,'superficie_hectareas'=>0.08,'ano_plantacion'=>2016]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Nectarin Bright Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SANE115->id,'superficie_hectareas'=>1.01,'ano_plantacion'=>2016]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Nectarin Agust Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SANE117->id,'superficie_hectareas'=>1.14,'ano_plantacion'=>2016]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'San Andrés - Nectarin Giant Pearl', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SANE118->id,'superficie_hectareas'=>1.13,'ano_plantacion'=>2016]);

// Santa Isabel
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno White Lady', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU101->id,'superficie_hectareas'=>0.09,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno Carson', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU102->id,'superficie_hectareas'=>0.52,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU103->id,'superficie_hectareas'=>0.42,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno Andross', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU104->id,'superficie_hectareas'=>0.96  ,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU111->id,'superficie_hectareas'=>0.18,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Durazno Spring Beauty', 'especie_id' => $peaches->id, 'centro_costo_id' => $SIDU112->id,'superficie_hectareas'=>0.5,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Artic Star', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE100->id,'superficie_hectareas'=>0.01,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Super Manon', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE105->id,'superficie_hectareas'=>1.45,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Super Manon', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE106->id,'superficie_hectareas'=>2.45,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE107->id,'superficie_hectareas'=>1.08,'ano_plantacion'=>2007]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE108->id,'superficie_hectareas'=>0.51,'ano_plantacion'=>2019]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE109->id,'superficie_hectareas'=>0.12,'ano_plantacion'=>2020]);
Cuartel::create(['tenant_id' => $tid,  'nombre' => 'Santa Isabel - Nectarin Artic Mist', 'especie_id' => $nectarines->id, 'centro_costo_id' => $SINE110->id,'superficie_hectareas'=>0.15,'ano_plantacion'=>2020]);




    }
}
