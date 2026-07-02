<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mantenedores;

use App\Exports\GenericEntityTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\CuartelImport;
use App\Imports\GenericEntityImport;
use App\Mail\WelcomeNewUser;
use App\Models\Actividad;
use App\Models\Aplicador;
use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\CentroCosto;
use App\Models\ClasificacionAgroquimico;
use App\Models\Cliente;
use App\Models\ContenedorCosecha;
use App\Models\Contratista;
use App\Models\Cuartel;
use App\Models\DireccionEnvio;
use App\Models\Empleado;
use App\Models\EquipoAplicacion;
use App\Models\Especie;
use App\Models\Familia;
use App\Models\Feriado;
use App\Models\ImplementoSeguridad;
use App\Models\IngredienteActivo;
use App\Models\ItemGasto;
use App\Models\Jornada;
use App\Models\MetodoPago;
use App\Models\Nebulizadora;
use App\Models\Producto;
use App\Models\ProductoSAG;
use App\Models\Proveedor;
use App\Models\SectorRiego;
use App\Models\Tarjeta;
use App\Models\TipoDocumento;
use App\Models\TractorMaquinaria;
use App\Models\Trato;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MantenedorController extends Controller
{
    private const ENTITIES = [
        'actividades' => [
            'page' => 'mantenedores/actividades',
            'title' => 'Actividades',
            'description' => 'Tareas realizables en faenas agrícolas',
            'endpoint' => '/mantenedores/actividades',
            'model' => Actividad::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:50', 'requiere_maquinaria' => 'nullable|boolean', 'presupuestable' => 'nullable|boolean', 'tipo_labor' => 'required|in:dia,trato', 'unidad_medida_id' => 'nullable|uuid', 'valor' => 'nullable|numeric', 'item_gasto_id' => 'nullable|uuid|exists:items_gasto,id'],
        ],
        'bodegas' => [
            'page' => 'mantenedores/simple',
            'title' => 'Bodegas',
            'description' => 'Administración de almacenes físicos',
            'endpoint' => '/mantenedores/bodegas',
            'model' => Bodega::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:100'],
        ],
        'categorias' => [
            'page' => 'mantenedores/simple',
            'title' => 'Categorías / Calibres',
            'description' => 'Escalas de medición de fruta cosechada',
            'endpoint' => '/mantenedores/categorias',
            'model' => Categoria::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:100'],
        ],
        'clasificacion-agroquimicos' => [
            'page' => 'mantenedores/simple',
            'title' => 'Clasificación Agroquímicos',
            'description' => 'Niveles toxicológicos y tipologías',
            'endpoint' => '/mantenedores/clasificacion-agroquimicos',
            'model' => ClasificacionAgroquimico::class,
            'rules' => ['nombre' => 'required|string|max:255', 'descripcion' => 'nullable|string|max:500'],
        ],
        'clientes' => [
            'page' => 'mantenedores/simple',
            'title' => 'Clientes',
            'description' => 'Datos de contacto y RUT de contrapartes comerciales',
            'endpoint' => '/mantenedores/clientes',
            'model' => Cliente::class,
            'rules' => ['rut' => 'required|string|max:20', 'razon_social' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'telefono' => 'nullable|string|max:50'],
        ],
        'contratistas' => [
            'page' => 'mantenedores/simple',
            'title' => 'Contratistas',
            'description' => 'Empresas y personas contratadas para servicios agrícolas',
            'endpoint' => '/mantenedores/contratistas',
            'model' => Contratista::class,
            'rules' => ['nombre' => 'required|string|max:255', 'rut' => 'required|string|max:20', 'razon_social' => 'required|string|max:255', 'email_contacto' => 'nullable|email|max:255'],
        ],
        'contenedores-cosecha' => [
            'page' => 'mantenedores/contenedores-cosecha',
            'title' => 'Contenedores de Cosecha',
            'description' => 'Bins, cajas de embalaje y bandejas',
            'endpoint' => '/mantenedores/contenedores-cosecha',
            'model' => ContenedorCosecha::class,
            'rules' => ['especie_id' => 'nullable|uuid', 'nombre' => 'required|string|max:255', 'unidades_por_bin' => 'nullable|integer|min:1', 'peso_bin_kg' => 'nullable|numeric|min:0'],
        ],
        'direcciones-envio' => [
            'page' => 'mantenedores/simple',
            'title' => 'Direcciones de Envío',
            'description' => 'Sedes físicas de despacho',
            'endpoint' => '/mantenedores/direcciones-envio',
            'model' => DireccionEnvio::class,
            'rules' => ['nombre' => 'required|string|max:255', 'direccion' => 'required|string|max:500', 'ciudad' => 'nullable|string|max:255', 'region' => 'nullable|string|max:255'],
        ],
        'feriados' => [
            'page' => 'mantenedores/simple',
            'title' => 'Feriados',
            'description' => 'Calendario de días no laborales',
            'endpoint' => '/mantenedores/feriados',
            'model' => Feriado::class,
            'rules' => ['nombre' => 'required|string|max:255', 'fecha' => 'required|date'],
        ],
        'implementos-seguridad' => [
            'page' => 'mantenedores/simple',
            'title' => 'Implementos de Seguridad',
            'description' => 'Equipos de protección personal (EPP)',
            'endpoint' => '/mantenedores/implementos-seguridad',
            'model' => ImplementoSeguridad::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:100'],
        ],
        'ingredientes-activos' => [
            'page' => 'mantenedores/simple',
            'title' => 'Ingredientes Activos',
            'description' => 'Catálogo de compuestos químicos',
            'endpoint' => '/mantenedores/ingredientes-activos',
            'model' => IngredienteActivo::class,
            'rules' => ['nombre' => 'required|string|max:255', 'descripcion' => 'nullable|string|max:500'],
        ],
        'items-gasto' => [
            'page' => 'mantenedores/simple',
            'title' => 'Ítems de Gasto',
            'description' => 'Plan de cuentas simplificado',
            'endpoint' => '/mantenedores/items-gasto',
            'model' => ItemGasto::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:100'],
        ],
        'jornadas' => [
            'page' => 'mantenedores/simple',
            'title' => 'Jornadas',
            'description' => 'Tipos de turnos de trabajo',
            'endpoint' => '/mantenedores/jornadas',
            'model' => Jornada::class,
            'rules' => ['nombre' => 'required|string|max:255', 'horas_jornada' => 'nullable|numeric'],
        ],
        'metodos-pago' => [
            'page' => 'mantenedores/simple',
            'title' => 'Métodos de Pago',
            'description' => 'Modos de liquidar tesorería',
            'endpoint' => '/mantenedores/metodos-pago',
            'model' => MetodoPago::class,
            'rules' => ['nombre' => 'required|string|max:255'],
        ],
        'nebulizadoras' => [
            'page' => 'mantenedores/simple',
            'title' => 'Nebulizadoras',
            'description' => 'Pulverizadores acoplados a tractores',
            'endpoint' => '/mantenedores/nebulizadoras',
            'model' => Nebulizadora::class,
            'rules' => ['nombre' => 'required|string|max:255', 'patente' => 'nullable|string|max:20', 'capacidad_litros' => 'nullable|numeric'],
        ],
        'proveedores' => [
            'page' => 'mantenedores/simple',
            'title' => 'Proveedores',
            'description' => 'Datos de contacto y RUT de proveedores',
            'endpoint' => '/mantenedores/proveedores',
            'model' => Proveedor::class,
            'rules' => ['rut' => 'required|string|max:20', 'razon_social' => 'required|string|max:255', 'clasificacion' => 'nullable|string|max:100', 'contacto_email' => 'nullable|email|max:255'],
        ],
        'sectores-riego' => [
            'page' => 'mantenedores/simple',
            'title' => 'Sectores de Riego',
            'description' => 'Agrupaciones hidráulicas de cuarteles',
            'endpoint' => '/mantenedores/sectores-riego',
            'model' => SectorRiego::class,
            'rules' => ['nombre' => 'required|string|max:255', 'caudal_disponible_l_s' => 'nullable|numeric'],
        ],
        'tipo-documentos' => [
            'page' => 'mantenedores/simple',
            'title' => 'Tipo de Documentos',
            'description' => 'Clasificación de facturas, boletas o guías',
            'endpoint' => '/mantenedores/tipo-documentos',
            'model' => TipoDocumento::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo_sii' => 'nullable|string|max:50'],
        ],
        'unidades' => [
            'page' => 'mantenedores/simple',
            'title' => 'Unidades',
            'description' => 'Unidades de volumen, peso y medida',
            'endpoint' => '/mantenedores/unidades',
            'model' => Unidad::class,
            'rules' => ['nombre' => 'required|string|max:255', 'abreviacion' => 'required|string|max:20'],
        ],
        'usuarios' => [
            'page' => 'mantenedores/simple',
            'title' => 'Usuarios',
            'description' => 'Permisos y correos de acceso a la plataforma',
            'endpoint' => '/mantenedores/usuarios',
            'model' => User::class,
            'rules' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:superadmin,admin,supervisor,operador',
                'agrupador' => 'nullable|string|max:100',
            ],
        ],
        'familias' => [
            'page' => 'mantenedores/familias',
            'title' => 'Familias',
            'description' => 'Clasificación botánica de cultivos',
            'endpoint' => '/mantenedores/familias',
            'model' => Familia::class,
            'rules' => ['nombre' => 'required|string|max:255', 'descripcion' => 'nullable|string|max:500'],
        ],
        'especies' => [
            'page' => 'mantenedores/especies',
            'title' => 'Especies',
            'description' => 'Especies botánicas asociadas a una familia',
            'endpoint' => '/mantenedores/especies',
            'model' => Especie::class,
            'rules' => ['nombre' => 'required|string|max:255', 'familia_id' => 'nullable|uuid', 'descripcion' => 'nullable|string|max:500'],
        ],
        'variedades' => [
            'page' => 'mantenedores/variedades',
            'title' => 'Variedades',
            'description' => 'Variedades de cultivo asociadas a una especie',
            'endpoint' => '/mantenedores/variedades',
            'model' => Variedad::class,
            'rules' => ['nombre' => 'required|string|max:255', 'especie_id' => 'nullable|uuid', 'descripcion' => 'nullable|string|max:500'],
        ],
        'cuarteles' => [
            'page' => 'mantenedores/paddocks',
            'title' => 'Cuarteles',
            'description' => 'Administración de parcelas y bloques productivos',
            'endpoint' => '/mantenedores/cuarteles',
            'model' => Cuartel::class,
            'rules' => [
                'nombre' => 'required|string|max:255',
                'centro_costo_id' => 'required|uuid|exists:centros_costo,id',
                'especie_id' => 'nullable|uuid|exists:especies,id',
                'superficie_hectareas' => 'required|numeric|min:0',
                'ano_plantacion' => 'required|integer|min:1900',
                'distancia_sobre_hilera' => 'required|numeric|min:0',
                'distancia_intra_hilera' => 'required|numeric|min:0',
                'geometria_geojson' => 'nullable|array',
            ],
        ],
        'empleados' => [
            'page' => 'mantenedores/employees',
            'title' => 'Empleados',
            'description' => 'Ficha de personal interno y subcontratado',
            'endpoint' => '/mantenedores/empleados',
            'model' => Empleado::class,
            'rules' => [
                'rut' => 'required|string|max:20',
                'nombre' => 'required|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'sueldo_base' => 'nullable|numeric',
                'valor_dia_base' => 'nullable|numeric',
                'valor_hora_extra' => 'nullable|numeric',
                'fecha_nacimiento' => 'nullable|date',
                'fecha_inicio_contrato' => 'nullable|date',
                'fecha_termino_contrato' => 'nullable|date',
                'trato_id' => 'nullable|string',
                'monto_trato' => 'nullable|numeric',
                'jefe_id' => 'nullable|string',
                'usuario_asociado_id' => 'nullable|string',
                'es_contratista' => 'nullable|boolean',
                'contratista_id' => 'nullable|uuid',
                'semana_corrida' => 'nullable|boolean',
                'trabaja_sueldo_liquido' => 'nullable|boolean',
                'trabajador_agricola' => 'nullable|boolean',
                'costos_sensibles' => 'nullable|boolean',
            ],
        ],
        'productos' => [
            'page' => 'mantenedores/products',
            'title' => 'Productos',
            'description' => 'Fichas de insumos, herramientas y agroquímicos',
            'endpoint' => '/mantenedores/productos',
            'model' => Producto::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo_barras' => 'nullable|string|max:100', 'categoria' => 'required|in:agroquimico,fertilizante,maquinaria_repuesto,combustible,EPP,otros', 'unidad_medida_id' => 'required|uuid|exists:unidades,id', 'ingrediente_activo' => 'nullable|string|max:255', 'dosis_recomendada_por_ha' => 'nullable|numeric|min:0', 'dias_carencia' => 'nullable|integer|min:0'],
        ],
        'centros-costo' => [
            'page' => 'mantenedores/cost-centers',
            'title' => 'Centros de Costo',
            'description' => 'Unidades financieras para acumulación de egresos',
            'endpoint' => '/mantenedores/centros-costo',
            'model' => CentroCosto::class,
            'rules' => ['codigo' => 'required|string|max:50', 'nombre' => 'required|string|max:255', 'agrupador' => 'nullable|string|max:255'],
        ],
        'tratos' => [
            'page' => 'mantenedores/extra-payment-types',
            'title' => 'Tratos',
            'description' => 'Configuración de tarifas y bonos por rendimiento',
            'endpoint' => '/mantenedores/tratos',
            'model' => Trato::class,
            'rules' => ['nombre' => 'required|string|max:255', 'codigo' => 'nullable|string|max:50'],
        ],
        'tractores' => [
            'page' => 'mantenedores/tractors',
            'title' => 'Tractores',
            'description' => 'Tractores, maquinaria y vehículos agrícolas',
            'endpoint' => '/mantenedores/tractores',
            'model' => TractorMaquinaria::class,
            'rules' => [
                'nombre' => 'required|string|max:255',
                'patente_o_identificador' => 'nullable|string|max:50',
                'tipo' => 'required|in:tractor,nebulizadora,rastra,vehiculo_carga',
                'horas_motor_iniciales' => 'nullable|numeric|min:0',
                'consumo_estimado_combustible_hora' => 'nullable|numeric|min:0',
            ],
        ],
        'tarjetas' => [
            'page' => 'mantenedores/tarjetas',
            'title' => 'Tarjetas',
            'description' => 'Tarjetas con QR para identificación en faenas',
            'endpoint' => '/mantenedores/tarjetas',
            'model' => Tarjeta::class,
            'rules' => [
                'codigo_qr' => 'nullable|string|max:20',
                'sigla' => 'required|string|max:10',
                'empleado_id' => 'nullable|uuid|exists:empleados,id',
                'activo' => 'boolean',
            ],
        ],
        'productos-sag' => [
            'page' => 'mantenedores/productos-sag',
            'title' => 'Productos SAG',
            'description' => 'Registro SAG de productos agroquímicos autorizados',
            'endpoint' => '/mantenedores/productos-sag',
            'model' => ProductoSAG::class,
            'rules' => [
                'producto_id' => 'required|uuid',
                'clasificacion_agroquimico_id' => 'nullable|uuid',
                'nro_autorizacion_sag' => 'required|string|max:50',
                'nombre_comercial' => 'required|string|max:255',
                'ingrediente_activo' => 'required|string|max:255',
                'titular' => 'nullable|string|max:255',
                'estado_sag' => 'required|string|max:20',
                'toxicidad_abejas' => 'nullable|string|max:20',
                'url_etiqueta' => 'nullable|string|max:500',
                'url_hds' => 'nullable|string|max:500',
                'ultima_actualizacion_sag' => 'nullable|date',
            ],
        ],
        'aplicadores' => [
            'page' => 'mantenedores/aplicadores',
            'title' => 'Aplicadores',
            'description' => 'Operadores capacitados para aplicar agroquímicos',
            'endpoint' => '/mantenedores/aplicadores',
            'model' => Aplicador::class,
            'rules' => [
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'rut' => 'required|string|max:20',
                'fecha_nacimiento' => 'nullable|date',
                'capacitado' => 'boolean',
                'certificado_url' => 'nullable|string|max:500',
                'activo' => 'boolean',
            ],
        ],
        'equipos-aplicacion' => [
            'page' => 'mantenedores/equipos-aplicacion',
            'title' => 'Equipos de Aplicación',
            'description' => 'Equipos para aplicación de agroquímicos',
            'endpoint' => '/mantenedores/equipos-aplicacion',
            'model' => EquipoAplicacion::class,
            'rules' => [
                'nombre' => 'required|string|max:255',
                'tipo' => 'required|string|max:30',
                'ultima_calibracion' => 'nullable|date',
                'proxima_calibracion' => 'nullable|date',
                'ultima_mantencion' => 'nullable|date',
                'proxima_mantencion' => 'nullable|date',
                'activo' => 'boolean',
            ],
        ],
    ];

    public function index(string $entity, Request $request)
    {
        $config = self::ENTITIES[$entity] ?? null;

        if (! $config) {
            abort(404);
        }

        $modelClass = $config['model'];
        $modelInstance = new $modelClass;
        $skip = ['id', 'tenant_id', 'parent_id', 'user_id', 'password', 'is_first_login', 'created_at', 'updated_at', 'deleted_at', 'activo'];
        $fillable = array_values(array_diff($modelInstance->getFillable(), $skip));
        $sortColumn = in_array('nombre', $fillable) ? 'nombre' : ($fillable[0] ?? 'id');
        $items = $modelClass::orderBy($sortColumn)->get();

        $fields = [];
        foreach ($fillable as $col) {
            $rules = $config['rules'][$col] ?? '';
            $isRequired = str_contains($rules, 'required');
            $isNumeric = str_contains($rules, 'numeric');
            $isEmail = str_contains($col, 'email');
            $type = $isEmail ? 'email' : ($isNumeric ? 'number' : 'text');
            $label = match ($col) {
                'abreviacion' => 'Abreviación',
                'codigo_sii' => 'Código SII',
                'razon_social' => 'Razón Social',
                'caudal_disponible_l_s' => 'Caudal (L/s)',
                'contacto_email' => 'Email',
                'horas_jornada' => 'Horas Jornada',
                'capacidad_litros' => 'Capacidad (L)',
                default => ucfirst(str_replace('_', ' ', $col)),
            };
            $field = [
                'name' => $col,
                'label' => $label,
                'type' => $type,
                'required' => $isRequired,
            ];

            if ($entity === 'usuarios' && $col === 'role') {
                $field['type'] = 'select';
                $field['options'] = [
                    ['value' => 'superadmin', 'label' => 'Super Admin'],
                    ['value' => 'admin', 'label' => 'Administrador'],
                    ['value' => 'supervisor', 'label' => 'Supervisor'],
                    ['value' => 'operador', 'label' => 'Operador'],
                ];
            }

            if ($entity === 'usuarios' && $col === 'agrupador') {
                $field['placeholder'] = 'Ej: Norte, Centro, Sur';
            }

            $fields[] = $field;
        }

        if ($entity === 'usuarios') {
            $fields[] = [
                'name' => 'password',
                'label' => 'Contraseña',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'Dejar vacío para generar automáticamente',
                'xs' => 12,
                'sm' => 6,
            ];
        }

        return Inertia::render($config['page'], [
            'items' => $items,
            'pageTitle' => $config['title'],
            'pageDescription' => $config['description'],
            'entityName' => $entity,
            'endpoint' => $config['endpoint'],
            'entityFields' => $fields,
        ]);
    }

    public function store(string $entity, Request $request)
    {
        $config = self::ENTITIES[$entity] ?? abort(404);

        try {
            $validated = $request->validate($config['rules']);

            $validated['tenant_id'] = auth()->user()->tenant_id;

            $modelClass = $config['model'];

            if ($entity === 'cuarteles') {
                $variedadesRaw = $request->input('_variedades', '[]');
                $variedades = json_decode($variedadesRaw, true) ?? [];
                unset($validated['_variedades']);
                $cuartel = $modelClass::create($validated);
                foreach ($variedades as $v) {
                    if (! empty($v['variedad_id']) && ! empty($v['cantidad_plantas'])) {
                        $cuartel->variedades()->attach($v['variedad_id'], [
                            'cantidad_plantas' => (int) $v['cantidad_plantas'],
                        ]);
                    }
                }
            } elseif ($entity === 'usuarios') {
                $rawPassword = $validated['password'] ?? \Illuminate\Support\Str::random(8);
                $validated['password'] = bcrypt($rawPassword);
                $user = $modelClass::create($validated);
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->send(new WelcomeNewUser($user, $rawPassword));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('No se pudo enviar email de bienvenida', [
                        'user' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $modelClass::create($validated);
            }

            Inertia::flash('toast', ['type' => 'success', 'message' => $config['title'].' creado correctamente.']);
        } catch (\Exception $e) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Error al crear: '.$e->getMessage()]);
        }

        return redirect()->back();
    }

    public function update(string $entity, string $id, Request $request)
    {
        $config = self::ENTITIES[$entity] ?? abort(404);

        try {
            $rules = $config['rules'];
            if ($entity === 'usuarios') {
                $rules['email'] = 'required|email|max:255|unique:users,email,'.$id;
            }
            $validated = $request->validate($rules);

            $modelClass = $config['model'];
            $item = $modelClass::findOrFail($id);

            if ($entity === 'cuarteles') {
                $variedadesRaw = $request->input('_variedades', '[]');
                $variedades = json_decode($variedadesRaw, true) ?? [];
                unset($validated['_variedades']);
                $item->update($validated);
                $sync = [];
                foreach ($variedades as $v) {
                    if (! empty($v['variedad_id']) && ! empty($v['cantidad_plantas'])) {
                        $sync[$v['variedad_id']] = ['cantidad_plantas' => (int) $v['cantidad_plantas']];
                    }
                }
                $item->variedades()->sync($sync);
            } elseif ($entity === 'usuarios') {
                if (! empty($validated['password'])) {
                    $validated['password'] = bcrypt($validated['password']);
                } else {
                    unset($validated['password']);
                }
                $item->update($validated);
            } else {
                $item->update($validated);
            }

            Inertia::flash('toast', ['type' => 'success', 'message' => $config['title'].' actualizado correctamente.']);
        } catch (\Exception $e) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Error al actualizar: '.$e->getMessage()]);
        }

        return redirect()->back();
    }

    public function destroy(string $entity, string $id)
    {
        $config = self::ENTITIES[$entity] ?? abort(404);

        try {
            $modelClass = $config['model'];
            $item = $modelClass::findOrFail($id);
            $item->delete();

            Inertia::flash('toast', ['type' => 'success', 'message' => $config['title'].' eliminado correctamente.']);
        } catch (\Exception $e) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Error al eliminar: '.$e->getMessage()]);
        }

        return redirect()->back();
    }

    public function toggleStatus(string $entity, string $id)
    {
        $config = self::ENTITIES[$entity] ?? abort(404);

        try {
            $modelClass = $config['model'];
            $item = $modelClass::findOrFail($id);

            if (in_array('activo', $item->getFillable())) {
                $item->update(['activo' => ! $item->activo]);
            }

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Estado actualizado correctamente.']);
        } catch (\Exception $e) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Error al cambiar estado: '.$e->getMessage()]);
        }

        return redirect()->back();
    }

    private function getEntityConfig(string $entity): array
    {
        if (! isset(self::ENTITIES[$entity])) {
            abort(404, "Entity '{$entity}' not found.");
        }

        return self::ENTITIES[$entity];
    }

    public function exportTemplate(string $entity): BinaryFileResponse
    {
        $config = $this->getEntityConfig($entity);

        if ($entity === 'cuarteles') {
            $headers = [
                'nombre',
                'centro_costo',
                'especie',
                'variedad',
                'cantidad_plantas',
                'superficie_hectareas',
                'ano_plantacion',
                'distancia_sobre_hilera',
                'distancia_intra_hilera',
            ];
        } else {
            $modelClass = $config['model'];
            $skip = ['id', 'tenant_id', 'created_at', 'updated_at', 'deleted_at'];
            $fillable = (new $modelClass)->getFillable();
            $headers = array_values(array_diff($fillable, $skip));
        }

        $export = new GenericEntityTemplateExport($headers, $config['title']);

        return Excel::download($export, "{$entity}-plantilla.xlsx");
    }

    public function import(string $entity, Request $request): RedirectResponse
    {
        $config = $this->getEntityConfig($entity);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        if ($entity === 'cuarteles') {
            $import = new CuartelImport(
                tenantId: auth()->user()->tenant_id,
            );
        } else {
            $import = new GenericEntityImport(
                modelClass: $config['model'],
                fieldMap: [],
                defaults: ['tenant_id' => auth()->user()->tenant_id],
                rules: $config['rules'] ?? [],
            );
        }

        Excel::import($import, $request->file('file'));

        $inserted = $import->inserted();
        $errors = $import->errors();
        $message = "{$inserted} registro(s) importado(s)";
        if (! empty($errors)) {
            $message .= ', '.count($errors).' error(es):';
            foreach (array_slice($errors, 0, 10) as $err) {
                $message .= " Fila {$err['row']}: {$err['message']}";
                logger()->warning("Import {$entity} row {$err['row']}: {$err['message']}");
            }
            if (count($errors) > 10) {
                $message .= ' ...y '.(count($errors) - 10).' error(es) más. Revisar log para detalles.';
            }

            return redirect()->back()->with('warning', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    public function batchDestroy(string $entity, Request $request): RedirectResponse
    {
        $config = $this->getEntityConfig($entity);
        $modelClass = $config['model'];

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No se seleccionaron registros.');
        }

        $count = $modelClass::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', "{$count} registro(s) eliminado(s).");
    }

    public function familias()
    {
        return Inertia::render('mantenedores/familias', [
            'items' => Familia::orderBy('nombre')->get(),
        ]);
    }

    public function especies()
    {
        return Inertia::render('mantenedores/especies', [
            'items' => Especie::with('familia')->orderBy('nombre')->get(),
            'familias' => Familia::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function actividades()
    {
        return Inertia::render('mantenedores/actividades', [
            'items' => Actividad::with('unidadMedida', 'itemGasto')->orderBy('nombre')->get(),
            'unidades' => Unidad::orderBy('nombre')->get(['id', 'nombre']),
            'itemsGasto' => ItemGasto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function variedades()
    {
        return Inertia::render('mantenedores/variedades', [
            'items' => Variedad::with('especie')->orderBy('nombre')->get(),
            'especies' => Especie::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function paddocks()
    {
        $centroCostos = CentroCosto::orderBy('nombre')->get(['id', 'nombre']);
        $especies = Especie::orderBy('nombre')->get(['id', 'nombre']);
        $variedades = Variedad::orderBy('nombre')->get(['id', 'nombre', 'especie_id']);

        $items = Cuartel::with(['centroCosto', 'especie', 'variedades'])->orderBy('nombre')->get();

        $items->transform(function ($cuartel) {
            $cuartel->_variedades = json_encode(
                $cuartel->variedades->map(fn ($v) => [
                    'variedad_id' => $v->id,
                    'cantidad_plantas' => $v->pivot->cantidad_plantas,
                ])->toArray()
            );

            return $cuartel;
        });

        return Inertia::render('mantenedores/paddocks', [
            'items' => $items,
            'centroCostos' => $centroCostos,
            'especies' => $especies,
            'variedades' => $variedades,
        ]);
    }

    public function employees()
    {
        return Inertia::render('mantenedores/employees', [
            'items' => Empleado::with('contratista')->orderBy('nombre')->get(),
            'contratistas' => Contratista::orderBy('nombre')->get(['id', 'nombre', 'rut']),
        ]);
    }

    public function products()
    {
        return Inertia::render('mantenedores/products', [
            'items' => Producto::with('unidadMedida')->orderBy('nombre')->get(),
            'unidades' => Unidad::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function costCenters()
    {
        return Inertia::render('mantenedores/cost-centers', [
            'items' => CentroCosto::orderBy('nombre')->get(),
        ]);
    }

    public function extraPaymentTypes()
    {
        return Inertia::render('mantenedores/extra-payment-types', [
            'items' => Trato::orderBy('nombre')->get(),
        ]);
    }

    public function tractors()
    {
        return Inertia::render('mantenedores/tractors', [
            'items' => TractorMaquinaria::orderBy('nombre')->get(),
        ]);
    }

    public function productosSag()
    {
        return Inertia::render('mantenedores/productos-sag', [
            'items' => ProductoSAG::with('producto', 'clasificacionAgroquimico', 'usos')->orderBy('nombre_comercial')->get(),
            'productos' => Producto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function aplicadoresMetodo()
    {
        return Inertia::render('mantenedores/aplicadores', [
            'items' => Aplicador::orderBy('nombres')->get(),
        ]);
    }

    public function equiposAplicacion()
    {
        return Inertia::render('mantenedores/equipos-aplicacion', [
            'items' => EquipoAplicacion::orderBy('nombre')->get(),
        ]);
    }

    public function unassignTarjeta(string $id)
    {
        try {
            $tarjeta = Tarjeta::findOrFail($id);
            $tarjeta->unassign(auth()->id());

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Tarjeta desasignada correctamente.']);
        } catch (\Exception $e) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Error al desasignar: '.$e->getMessage()]);
        }

        return redirect()->back();
    }

    public function cards()
    {
        return Inertia::render('mantenedores/tarjetas', [
            'items' => Tarjeta::with('empleado')->orderBy('codigo_qr')->get(),
            'empleados' => Empleado::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'rut']),
        ]);
    }

    public function harvestContainers()
    {
        return Inertia::render('mantenedores/contenedores-cosecha', [
            'items' => ContenedorCosecha::with('especie')->orderBy('nombre')->get(),
            'especies' => Especie::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }
}
