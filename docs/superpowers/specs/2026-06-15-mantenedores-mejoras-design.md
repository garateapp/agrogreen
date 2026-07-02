# Mejoras a Mantenedores — Spec de Diseño

## 1. Resumen

Extender el sistema de mantenedores (34 entidades) con:
- Especie/Variedad como FK en Cuarteles (migrando datos existentes)
- Subida masiva vía Excel/CSV por entidad
- Multi-select + eliminación por lote
- Filtros dinámicos por campo

---

## 2. Cuartel: Especie y Variedad como FK

### Migración

`database/migrations/2026_06_15_000061_add_especie_variedad_to_cuartels.php`

- Agregar `especie_id` (FK→especies, nullable, SET NULL on delete)
- Agregar `variedad_id` (FK→variedades, nullable, SET NULL on delete)
- Migrar datos: `especie_cultivo` → match name contra `especies.nombre`, set `especie_id`. Si no hay match, queda NULL.
- Migrar datos: `variedad` → match name contra `variedades.nombre`, set `variedad_id`. Si no hay match, queda NULL.
- Eliminar columnas `especie_cultivo` y `variedad`

### Modelo `Cuartel.php`

- Agregar `belongsTo(Especie::class)` y `belongsTo(Variedad::class)`
- Agregar `especie_id`, `variedad_id` a `$fillable`
- Remover `especie_cultivo`, `variedad` de `$fillable`

### Controller (`MantenedorController::paddocks()`)

- Eager load `especie`, `variedad`
- Pasar `especies` y `variedades` a la vista (para selects)

### Frontend (`paddocks.tsx` y `MantenedorFieldFactory.tsx`)

- Reemplazar inputs texto de especie/variedad por `<select>` con opciones desde backend
- Variedad en cascada: **client-side** — el controller pasa TODAS las especies y TODAS las variedades a la vista como arrays planos. El frontend filtra las variedades por `especie_id` seleccionado usando JS, sin llamada adicional al servidor. Esto es viable porque son datasets pequeños (<100 registros cada uno).
- Los campos se definen como `type: 'select'` tanto en formulario como en filtros, con `options` precargadas. El formulario de edición/creación de cuartel usa lógica adicional de cascada (al cambiar especie, filtra opciones de variedad). El filtro muestra ambos campos como select independientes (sin cascada).

---

## 3. Bulk Upload (Excel/CSV)

### Dependencias

- `composer require maatwebsite/laravel-excel`

### Backend — Exportador de Plantilla

`app/Exports/GenericEntityTemplateExport.php`
- Recibe el config de la entidad (fields, title)
- Genera un Excel con headers = labels de los fields
- Bypass: excluir campos `id`, campos de sistema (`tenant_id`, timestamps)

### Backend — Importador Genérico

`app/Imports/GenericEntityImport.php`
- Implementa `ToModel`, `WithHeadingRow`, `WithValidation`, `WithBatchInserts`, `WithChunkReading`
- Recibe en constructor: `array $entityConfig` (model class, fields, rules)
- Dinámicamente arma reglas de validación desde `$entityConfig['rules']`
- Mapea headers (labels del Excel) a field names
- Asigna `tenant_id` automáticamente
- Retorna estadísticas: insertados, errores por fila

### Controller (`MantenedorController`)

Agregar métodos:
- `exportTemplate(string $entity)` — descarga Excel con headers de la entidad
- `import(string $entity, Request $request)` — recibe archivo, ejecuta import, retorna resultado

### Rutas (dentro del grupo `mantenedores`)

```
GET  /mantenedores/{entity}/template  → exportTemplate
POST /mantenedores/{entity}/import     → import
```

Estas rutas van ANTES de `{entity}/{id}` para evitar conflictos.

### Frontend — Modal de Importación

`resources/js/components/mantenedores/MantenedorImportModal.tsx`

Flujo en 3 pasos:
1. **Descargar plantilla** — botón que descarga el Excel template de la entidad actual
2. **Subir archivo** — drag-and-drop o file picker (solo .xlsx, .csv)
3. **Confirmar** — muestra resumen de filas detectadas, botón "Importar"

Estados: idle → file_selected → preview → importing → done/error
Indicador de progreso durante la subida.

### Integración en MantenedorListPage

- Botón "Importar" en el header (junto al search/toggle)
- Al hacer clic, abre MantenedorImportModal

---

## 4. Multi-Select + Bulk Delete

### Backend

`MantenedorController::batchDestroy(string $entity, Request $request)`
- Recibe `{ ids: string[] }`
- Soft-delete masivo: `Model::whereIn('id', $ids)->delete()`
- Retorna redirect back con mensaje de éxito

### Ruta

```
DELETE /mantenedores/{entity}/batch  → batchDestroy
```
Registrada ANTES de `{entity}/{id}`.

### Frontend

`MantenedorCard.tsx`
- Agregar checkbox en la esquina superior izquierda de la card
- Checkbox se muestra al hacer hover y cuando algún item está seleccionado

`MantenedorListPage.tsx`
- Estado `selectedIds: Set<string>` (o `string[]`)
- Pasar `isSelected` y `onToggleSelect` a cada card
- `onToggleSelect`: agrega/remueve del set
- `onSelectAll`: selecciona todos los items visibles (filtrados)
- Cuando `selectedIds.length > 0`, mostrar batch toolbar:

**BatchToolbar** (`MantenedorBatchToolbar.tsx`)
- Barra inferior fija (o flotante)
- Texto: "N registros seleccionados"
- Botón "Seleccionar todos los N" (selecciona items filtrados)
- Botón "Eliminar seleccionados" → confirm dialog → DELETE /batch
- Botón "Cancelar selección"

---

## 5. Filtros Dinámicos

### Frontend — MantenedorFilterBar

`resources/js/components/mantenedores/MantenedorFilterBar.tsx`

Renderizado reactivo desde `fields[]`. Los tipos de campo son los mismos que los del formulario (`text`, `number`, `select`, `textarea`, `email`, `rut`, `date`, `switch`), no se introducen tipos nuevos:

- `type: 'select'` → dropdown con opciones únicas extraídas de los datos (más "Todos"). Las opciones precargadas (`field.options`) se usan como hint; si no existen, se extraen de los datos mismos. Esto aplica a especie, variedad y cualquier otro select.
- `type: 'text'` → input de texto
- `type: 'number'` → rango min/max (dos inputs)
- `type: 'date'` → date picker rango
- `type: 'boolean'` / `'switch'` → toggle/dropdown Sí/No/Todos
- `type: 'email'`, `'rut'` → input de texto (filtra por substring)
- `type: 'textarea'` → input de texto (filtra por substring)

Comportamiento:
- Colapsable: botón "Filtros" togglea visibilidad
- Badge con count de filtros activos
- Botón "Limpiar filtros" cuando hay alguno activo
- Filtrado 100% client-side sobre los items cargados vía Inertia

### Filtering engine (dentro de MantenedorListPage)

- `filters` state: `Record<string, any>`
- `activeFiltersCount`: cuenta keys con valor no-vacío/no-"todos"
- Computed `filteredItems`: aplica todos los filtros + search text sobre `items`
- La lógica de filtrado:
  - select: match exacto
  - text/email/rut: `item[field].toLowerCase().includes(value.toLowerCase())`
  - number (range): `item[field] >= min && item[field] <= max`
  - date (range): `new Date(item[field]) >= min && new Date(item[field]) <= max`
  - boolean: match exacto (true/false/null)

---

## 6. Archivos Afectados

### Nuevos archivos

| Archivo | Propósito |
|---|---|
| `database/migrations/2026_06_15_000061_add_especie_variedad_to_cuartels.php` | Migración Cuartel |
| `app/Imports/GenericEntityImport.php` | Importador Excel genérico |
| `app/Exports/GenericEntityTemplateExport.php` | Exportador plantilla Excel |
| `resources/js/components/mantenedores/MantenedorImportModal.tsx` | Modal de subida masiva |
| `resources/js/components/mantenedores/MantenedorFilterBar.tsx` | Barra de filtros dinámicos |
| `resources/js/components/mantenedores/MantenedorBatchToolbar.tsx` | Toolbar de acciones por lote |

### Archivos modificados

| Archivo | Cambios |
|---|---|
| `app/Models/Cuartel.php` | Relaciones especie/variedad, fillable |
| `app/Http/Controllers/Mantenedores/MantenedorController.php` | paddocks(), import, exportTemplate, batchDestroy |
| `routes/web.php` | Nuevas rutas batch/template/import |
| `resources/js/pages/mantenedores/paddocks.tsx` | Fields con especie/variedad como select |
| `resources/js/components/mantenedores/MantenedorListPage.tsx` | Selección, filtros, botón importar |
| `resources/js/components/mantenedores/MantenedorCard.tsx` | Checkbox |
| `resources/js/components/mantenedores/mantenedor-types.ts` | Tipos para filtros (opcional) |
| `composer.json` | maatwebsite/excel |

---

## 7. Decisiones Técnicas

- **Filtrado client-side**: las entidades mantenedoras tienen pocos registros (<500), no justifica server-side aún. Si escala, se migra a server-side.
- **Importación genérica**: un solo importador que recibe el config de cada entidad, evitando N clases de import. Las reglas de validación se construyen desde `rules` del config.
- **Checkbox en cards**: visible en hover + cuando hay selección activa, para mantener la estética limpia por defecto.
- **Batch delete route**: se registra antes que `{entity}/{id}` para que "batch" no sea atrapado como un UUID.
- **No se elimina la funcionalidad existente**: solo se extiende. Los cards, FAB, modales individuales siguen funcionando igual.
