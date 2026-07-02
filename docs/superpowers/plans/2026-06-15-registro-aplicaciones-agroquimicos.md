# Registro de Aplicaciones de Agroquímicos — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build complete SAG-compliant agrochemical application registration module with product master linked to inventory, lot tracking with expiration, stock validation on create, and stock deduction on approval.

**Architecture:** 
- `productos_sag` linked to existing `productos` (inventory items) via FK `producto_id`
- `lotes` table for lot-level tracking (stock + expiration per lot)
- MantenedorController entries for `productos-sag`, `aplicadores`, `equipos-aplicacion`
- ApplicationRecordController for application lifecycle with inventory integration
- Stock validation at creation, stock deduction at approval via `inventario_movimientos`

**Tech Stack:** Laravel 11, MySQL, Inertia + React + MUI (DataGrid, Stepper, Autocomplete, DatePicker, Chip, Alert, Tabs)

---

### Task 1: Migrations — All tables

**Files:**
- Create: `database/migrations/2026_06_15_000050_create_productos_sag_table.php`
- Create: `database/migrations/2026_06_15_000051_create_producto_sag_usos_table.php`
- Create: `database/migrations/2026_06_15_000052_create_lotes_table.php`
- Create: `database/migrations/2026_06_15_000053_create_aplicadores_table.php`
- Create: `database/migrations/2026_06_15_000054_create_equipos_aplicacion_table.php`
- Create: `database/migrations/2026_06_15_000055_create_application_records_table.php`
- Create: `database/migrations/2026_06_15_000056_create_application_record_productos_table.php`
- Create: `database/migrations/2026_06_15_000057_create_application_weather_conditions_table.php`
- Create: `database/migrations/2026_06_15_000058_create_application_safety_checks_table.php`
- Create: `database/migrations/2026_06_15_000059_create_application_container_disposals_table.php`
- Create: `database/migrations/2026_06_15_000060_add_lote_id_to_inventario_movimiento_detalles_table.php`

- [ ] **Step 1: Create productos_sag migration** (uuid PK, tenant_id FK, producto_id FK→productos UNIQUE, clasificacion_agroquimico_id FK, nro_autorizacion_sag unique, nombre_comercial, ingrediente_activo, titular nullable, estado_sag enum, toxicidad_abejas enum nullable, url_etiqueta nullable, url_hds nullable, ultima_actualizacion_sag date nullable, timestamps + softDeletes)

- [ ] **Step 2: Create producto_sag_usos migration** (FK producto_sag, FK categoria nullable, objetivo string, dosis_min/max decimal, unidad_dosis string, carencia_dias int, reingreso_horas int, restricciones text nullable, timestamps)

- [ ] **Step 3: Create lotes migration** (uuid PK, tenant_id FK, producto_id FK→productos, codigo_lote string, fecha_vencimiento date nullable, cantidad_inicial decimal(12,4), cantidad_disponible decimal(12,4), timestamps + softDeletes)

- [ ] **Step 4: Create aplicadores migration** (uuid PK, tenant_id FK, nombres string, apellidos string, rut string unique, fecha_nacimiento date, capacitado bool, certificado_url nullable, activo bool, timestamps + softDeletes)

- [ ] **Step 5: Create equipos_aplicacion migration** (uuid PK, tenant_id FK, nombre, tipo enum(mochila,nebulizadora,pulverizadora,dron,avion,otro), ultima_calibracion date nullable, proxima_calibracion date nullable, ultima_mantencion date nullable, proxima_mantencion date nullable, activo bool, timestamps + softDeletes)

- [ ] **Step 6: Create application_records migration** (uuid PK, tenant_id FK, codigo string unique, cuartel_id FK→cuartels, variedad_id FK→variedades nullable, temporada nullable, superficie decimal, fecha_aplicacion date, hora_inicio time nullable, hora_termino time nullable, estado enum, objetivo_tipo enum, objetivo_nombre nullable, responsable_id FK→users, aplicador_id FK→aplicadores nullable, supervisor_id FK→users nullable, equipo_id FK→equipos_aplicacion nullable, observaciones text nullable, aprobado_por FK→users nullable, aprobado_en datetime nullable, anulado_por FK→users nullable, motivo_anulacion text nullable, creado_por FK→users, timestamps + softDeletes)

- [ ] **Step 7: Create application_record_productos migration** (FK application_record, FK producto_sag, FK lote nullable, dosis decimal, unidad_dosis, cantidad_total decimal, volumen_agua decimal nullable, label_snapshot json nullable, timestamps)

- [ ] **Step 8: Create application_weather_conditions migration** (FK application_record unique, temperatura/humedad/viento_velocidad decimal nullable, viento_direccion string nullable, estado_general string nullable, riesgo_deriva enum nullable, fuente enum(manual,estacion,api) default manual, timestamps)

- [ ] **Step 9: Create application_safety_checks migration** (FK application_record unique, 5 epp booleans, senalizacion_instalada bool, agua_emergencia bool nullable, observaciones text nullable, timestamps)

- [ ] **Step 10: Create application_container_disposals migration** (FK application_record, FK producto_sag, envases_usados int, capacidad_envase decimal nullable, triple_lavado bool, almacenamiento_temporal string nullable, metodo_disposicion string nullable, documento_respaldo_url string nullable, timestamps)

- [ ] **Step 11: Add lote_id to inventario_movimiento_detalles** (FK lote_id nullable, with index)

- [ ] **Step 12: Run all migrations** `php artisan migrate --force`

---

### Task 2: Models

**Files:**
- Create: `app/Models/ProductoSAG.php`
- Create: `app/Models/ProductoSAGUso.php`
- Create: `app/Models/Lote.php`
- Create: `app/Models/Aplicador.php`
- Create: `app/Models/EquipoAplicacion.php`
- Create: `app/Models/ApplicationRecord.php`
- Create: `app/Models/ApplicationRecordProducto.php`
- Create: `app/Models/ApplicationWeatherCondition.php`
- Create: `app/Models/ApplicationSafetyCheck.php`
- Create: `app/Models/ApplicationContainerDisposal.php`

- [ ] **Step 1: Create ProductoSAG model** (BelongsToTenant, HasUuids, SoftDeletes; $table = 'productos_sag'; fillable; cast estado_sag, toxicidad_abejas; relaciones: producto(), clasificacionAgroquimico(), usos())

- [ ] **Step 2: Create ProductoSAGUso model** (HasUuids; $table = 'producto_sag_usos'; fillable; relaciones: productoSAG(), categoria())

- [ ] **Step 3: Create Lote model** (BelongsToTenant, HasUuids, SoftDeletes; $table = 'lotes'; fillable; cast cantidad_inicial/disponible to decimal; scope: conStock(), noVencido(); relaciones: producto())

- [ ] **Step 4: Create Aplicador model** (BelongsToTenant, HasUuids, SoftDeletes; fillable; cast fecha_nacimiento to date, capacitado/activo to bool; scopes: activo(), capacitado(); accessor: edad())

- [ ] **Step 5: Create EquipoAplicacion model** (BelongsToTenant, HasUuids, SoftDeletes; fillable; cast dates; scope: activo(), calibracionVigente(); accessor: calibracionVigente bool)

- [ ] **Step 6: Create ApplicationRecord model** (BelongsToTenant, HasUuids, SoftDeletes; fillable + casts; all relationships; methods: aprobar() creates inventario_movimiento + deducts stock, anular() reverts stock, calcularCarencia(), calcularReingreso())

- [ ] **Step 7: Create remaining models** (ApplicationRecordProducto, WeatherCondition, SafetyCheck, ContainerDisposal)

---

### Task 3: Mantenedores CRUD — Productos SAG, Aplicadores, Equipos

**Files:**
- Modify: `app/Http/Controllers/Mantenedores/MantenedorController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add `productos-sag` to ENTITIES** — model ProductoSAG, include producto_id in rules

- [ ] **Step 2: Add `aplicadores` and `equipos-aplicacion` to ENTITIES**

- [ ] **Step 3: Add GET routes** for productos-sag, aplicadores, equipos-aplicacion in web.php mantenedores group

- [ ] **Step 4: Add sidebar entries** in SidebarNavigation.tsx

---

### Task 4: ApplicationRecord Controller

**Files:**
- Create: `app/Http/Controllers/Agroquimicos/ApplicationRecordController.php`
- Create: `app/Http/Requests/Agroquimicos/StoreApplicationRecordRequest.php`
- Modify: `app/Providers/AppServiceProvider.php` (register policy)
- Modify: `routes/web.php`

- [ ] **Step 1: Create controller** with: index() filtered list, create() load relations for stepper, store() validate stock + save with transaction, show() with all relations, approve() create movement + deduct stock, cancel() revert stock, destroy() soft delete

- [ ] **Step 2: Create FormRequest** with validation for all nested data (productos, weather, safety, containers) and custom rule for stock validation

- [ ] **Step 3: Create Policy** ApplicationRecordPolicy with viewAny/create/view/update/delete/approve/cancel

- [ ] **Step 4: Register routes** in web.php under agroquimicos prefix

---

### Task 5: Frontend — Application List and Creation

**Files:**
- Create: `resources/js/pages/agroquimicos/index.tsx`
- Create: `resources/js/pages/agroquimicos/create.tsx`
- Create: `resources/js/pages/agroquimicos/show.tsx`
- Create: `resources/js/components/agroquimicos/index.ts` (types)
- Create: `resources/js/components/agroquimicos/StepUbicacion.tsx`
- Create: `resources/js/components/agroquimicos/StepProducto.tsx`
- Create: `resources/js/components/agroquimicos/StepDosis.tsx`
- Create: `resources/js/components/agroquimicos/StepPersonasEquipo.tsx`
- Create: `resources/js/components/agroquimicos/StepClima.tsx`
- Create: `resources/js/components/agroquimicos/StepSeguridad.tsx`
- Create: `resources/js/components/agroquimicos/StepRevision.tsx`

- [ ] **Step 1: Create types** with TypeScript interfaces matching backend

- [ ] **Step 2: Create index.tsx** — DataGrid with columns, status chips, filters, "Nueva Aplicación" button

- [ ] **Step 3: Create create.tsx** — Stepper with 7 steps, React Hook Form + Zod, stock validation before submit, disabled until stock OK

- [ ] **Step 4: Create all step components** with Autocomplete, DatePicker, TimePicker, Alert chips

---

### Task 6: Frontend — Detail Page with Tabs

**Files:**
- Create: `resources/js/pages/agroquimicos/show.tsx`
- Create: `resources/js/components/agroquimicos/DatosGeneralesTab.tsx`
- Create: `resources/js/components/agroquimicos/ProductosTab.tsx`
- Create: `resources/js/components/agroquimicos/ClimaSeguridadTab.tsx`
- Create: `resources/js/components/agroquimicos/EnvasesTab.tsx`
- Create: `resources/js/components/agroquimicos/AuditoriaTab.tsx`

- [ ] **Step 1: Create show.tsx** — fetch record with all relations, render Tabs with state chips, approve/cancel buttons

- [ ] **Step 2-5: Create tab components** for each section with read-only formatted data

---

### Task 7: Routes and Sidebar

**Files:**
- Modify: `routes/web.php`
- Modify: `resources/js/components/SidebarNavigation.tsx`

- [ ] **Step 1: Add remaining routes** (approve, cancel, export)

- [ ] **Step 2: Add sidebar entry** "Agroquímicos" with submenu

---

### Task 8: Seed Data

**Files:**
- Modify: `database/seeders/MantenedoresSeeder.php`

- [ ] **Step 1: Add seed data** for 5+ productos-sag (linked to existing productos), 10+ lotes, 3 aplicadores, 3 equipos

- [ ] **Step 2: Add 1-2 sample application records** with complete data

- [ ] **Step 3: Run seed** `php artisan db:seed`

---

### Task 9: Build Verification

- [ ] **Step 1: Run migrations** `php artisan migrate --force`

- [ ] **Step 2: Build frontend** `npm run build`

- [ ] **Step 3: Test CRUD** navigate to each new page, verify operations
