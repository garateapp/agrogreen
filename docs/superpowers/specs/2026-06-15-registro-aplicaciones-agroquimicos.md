# Registro de Aplicaciones de Agroquímicos / Plaguicidas

## Objetivo

Sistema de trazabilidad completa de uso de plaguicidas agrícolas, conforme a normativa SAG chilena (Resolución Exenta N°243/2025). Cubre: registro de productos, planificación, ejecución, condiciones climáticas, seguridad, envases, carencia y reingreso.

## Arquitectura

- **Mantenedores** (MantenedorController + MantenedorListPage): `productos-sag`, `aplicadores`, `equipos-aplicacion`
- **Controladores separados**: `ApplicationRecordController` para el ciclo completo de aplicación
- Frontend: React + MUI DataGrid (listados), Stepper (creación/edición de aplicación)

## Tablas

### Maestros

**productos_sag** — Productos SAG con datos normativos
- `id` uuid PK, `tenant_id` FK, `clasificacion_agroquimico_id` FK
- `nro_autorizacion_sag` string unique, `nombre_comercial`, `ingrediente_activo`, `titular` nullable
- `estado_sag` enum(authorizado, restringido, prohibido, cancelado)
- `toxicidad_abejas` enum(baja, moderada, alta, sin_datos) nullable
- `url_etiqueta`, `url_hds` nullable
- `ultima_actualizacion_sag` date nullable

**producto_sag_usos** — Cultivos y objetivos autorizados por producto
- `id` uuid PK, `producto_sag_id` FK, `categoria_id` FK nullable
- `objetivo` string, `dosis_min`, `dosis_max` decimal, `unidad_dosis`
- `carencia_dias` integer, `reingreso_horas` integer
- `restricciones` text nullable

**aplicadores** — Operadores capacitados
- `id` uuid PK, `tenant_id` FK
- `nombres`, `apellidos`, `rut` unique, `fecha_nacimiento` date
- `capacitado` boolean, `certificado_url` nullable, `activo` boolean

**equipos_aplicacion** — Equipos de aplicación
- `id` uuid PK, `tenant_id` FK
- `nombre`, `tipo` enum(mochila, nebulizadora, pulverizadora, dron, avion, otro)
- `ultima_calibracion`, `proxima_calibracion`, `ultima_mantencion`, `proxima_mantencion` nullable
- `activo` boolean

### Operacionales

**application_records** — Registro principal de aplicación
- `id` uuid PK, `tenant_id` FK, `codigo` string unique
- `cuartel_id` FK, `variedad_id` FK nullable
- `temporada` nullable, `superficie` decimal
- `fecha_aplicacion` date, `hora_inicio` time nullable, `hora_termino` time nullable
- `estado` enum(borrador, programada, ejecutada, en_revision, aprobada, observada, anulada)
- `objetivo_tipo` enum, `objetivo_nombre` nullable
- `responsable_id` FK users, `aplicador_id` FK, `supervisor_id` FK users nullable
- `equipo_id` FK nullable
- `observaciones` text nullable
- Auditoría: `creado_por`, `aprobado_por`, `aprobado_en`, `anulado_por`, `motivo_anulacion`

**application_record_productos** — Productos aplicados
- `id` uuid PK, `application_record_id` FK, `producto_sag_id` FK
- `lote`, `fecha_vencimiento` nullable
- `dosis` decimal, `unidad_dosis`, `cantidad_total` decimal, `volumen_agua` nullable
- `label_snapshot` json nullable

**application_weather_conditions** — Clima al aplicar
- `id` uuid PK, `application_record_id` FK unique
- `temperatura`, `humedad`, `viento_velocidad` decimal nullable
- `viento_direccion`, `estado_general` nullable
- `riesgo_deriva` enum(bajo, medio, alto) nullable
- `fuente` enum(manual, estacion, api)

**application_safety_checks** — Seguridad y EPP
- `id` uuid PK, `application_record_id` FK unique
- `epp_guantes`, `epp_mascarilla`, `epp_overol`, `epp_botas`, `epp_proteccion_ocular` boolean
- `senalizacion_instalada` boolean, `agua_emergencia` boolean nullable
- `observaciones` text nullable

**application_container_disposals** — Envases y residuos
- `id` uuid PK, `application_record_id` FK, `producto_sag_id` FK
- `envases_usados` integer, `capacidad_envase` decimal nullable
- `triple_lavado` boolean
- `almacenamiento_temporal`, `metodo_disposicion` nullable
- `documento_respaldo_url` nullable

## Pantallas

1. **Listado de Aplicaciones**: MUI DataGrid con filtros por predio, cuartel, cultivo, producto, estado. Chips de estado, alertas de carencia/reingreso. Botón "Nueva aplicación".

2. **Nueva/Editar Aplicación**: Stepper MUI con pasos:
   - Ubicación y cultivo
   - Producto y objetivo
   - Dosis y mezcla
   - Personas y equipo
   - Condiciones climáticas
   - Seguridad y envases
   - Revisión final

3. **Detalle de Aplicación**: Tabs con resumen, productos, clima, seguridad, envases, auditoría.

4. **Alertas**: Dashboard con alertas de carencia vigente, reingreso activo, equipos sin calibración, aplicadores sin capacitación.

## Validaciones clave

- Producto debe estar autorizado (estado_sag = autorizado)
- Cultivo debe estar en usos del producto
- Dosis dentro del rango permitido
- Aplicador debe estar activo y capacitado
- Equipo debe tener calibración vigente
- Alerta por viento alto / temperatura extrema
- Cálculo automático de fecha de reingreso y carencia

## Roles

- **admin**: configura maestros, usuarios
- **responsable_tecnico**: crea, revisa, aprueba aplicaciones
- **supervisor**: registra aplicaciones ejecutadas
- **aplicador**: ve aplicaciones asignadas
- **auditor**: solo lectura y exportación
