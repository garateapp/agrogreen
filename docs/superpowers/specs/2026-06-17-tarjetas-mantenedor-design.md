# Mantenedor de Tarjetas (QR)

**Date:** 2026-06-17

## Overview

Each employee receives a physical card with a QR code for identification in the faenas module (tarja scanning). The QR code format is `AG-` followed by a zero-padded 5-digit sequential number (e.g., `AG-00001`). A card can be assigned to one employee at a time; if reassigned, the previous card is automatically deactivated.

## Data Model — `tarjetas`

| Column | Type | Description |
|---|---|---|
| `id` | UUID PK | |
| `tenant_id` | UUID FK → tenants | Multi-tenant |
| `codigo_qr` | string, unique, not null | Auto-generated: `AG-XXXXX` |
| `empleado_id` | UUID FK → empleados, nullable | Current assignee (nullable = available/unassigned) |
| `fecha_asignacion` | timestamp, nullable | When it was assigned to the current employee |
| `activo` | boolean, default true | `false` = lost, damaged, or retired |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | softDeletes | |

Index: `[tenant_id, activo]`, unique on `codigo_qr`.

## Key Behaviors

1. **QR auto-generation**: On `store`, find max numeric value from existing `codigo_qr` patterns (`AG-(\d+)`), increment by 1, format to `AG-` + 5-digit zero-padded. Seed starts at `AG-00001`.
2. **Reassignment**: When a card is created/updated with `empleado_id`, if that employee already has another active card, that card is auto-deactivated (`activo = false`, `fecha_asignacion = null`). The new/updated card gets `fecha_asignacion = now()`.
3. **Unassign**: If `empleado_id` is cleared, `fecha_asignacion` becomes null.
4. **Deactivation**: Cards are soft-deactivated via `activo` toggle, not hard-deleted.

## Backend Changes

### MantenedorController.php
- Add `tarjetas` entry to `ENTITIES` array with validation rules:
  - `codigo_qr`: `required|string|unique:tarjetas,codigo_qr`
  - `empleado_id`: `nullable|exists:empleados,id`
  - `activo`: `boolean`
- Add dedicated `cards()` method that passes `empleados` (only active ones) for the select dropdown.
- Override `store()` and `update()` logic for auto-generating `codigo_qr` and handling reassignment.

### Tarjeta Model (`app/Models/Tarjeta.php`)
- Traits: `BelongsToTenant`, `HasUuids`, `SoftDeletes`
- `$fillable`: `codigo_qr`, `empleado_id`, `fecha_asignacion`, `activo`
- `$casts`: `fecha_asignacion => datetime`, `activo => boolean`
- Relationships:
  - `empleado(): BelongsTo`

### Empleado Model (add)
- `tarjetas(): HasMany`

## Frontend — `resources/js/pages/mantenedores/tarjetas.tsx`

Pattern follows `employees.tsx` (complex entity):

```tsx
const fields: MantenedorField[] = [
  { name: 'codigo_qr', label: 'Código QR', type: 'text' },
  { name: 'empleado_id', label: 'Empleado', type: 'select',
    options: empleados.map(e => ({ value: e.id, label: `${e.nombre} ${e.apellido} (${e.rut})` })) },
  { name: 'activo', label: 'Activa', type: 'switch' },
];
```

`codigo_qr` will be read-only on edit (already generated), auto-generated on create.

## Sidebar

Add to Mantenedores section in `SidebarNavigation.tsx`:
```tsx
{ title: 'Tarjetas', href: '/mantenedores/tarjetas' },
```

## Routes

Add named route in the mantenedores group before generic routes:
```php
Route::get('tarjetas', [MantenedorController::class, 'cards'])->name('tarjetas');
```

## Migration

File: `database/migrations/2026_06_17_000000_create_tarjetas_table.php`
