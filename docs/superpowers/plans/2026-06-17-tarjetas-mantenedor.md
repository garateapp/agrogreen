# Mantenedor de Tarjetas — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a "Tarjetas" CRUD mantenedor with auto-generated QR codes (`AG-XXXXX`) assignable to employees.

**Architecture:** New `tarjetas` table + Eloquent model with boot events for QR auto-generation and reassignment logic. Dedicated frontend page following `employees.tsx` pattern. Generic controller CRUD handles store/update (model boot handles special logic). Named route + sidebar link.

**Tech Stack:** Laravel 10+, Inertia + React + MUI, MySQL

---

### Task 1: Create migration for `tarjetas` table

**Files:**
- Create: `database/migrations/2026_06_17_000000_create_tarjetas_table.php`

- [ ] **Write migration**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('codigo_qr', 20)->unique();
            $table->foreignUuid('empleado_id')->nullable()->constrained('empleados')->onDelete('set null');
            $table->timestamp('fecha_asignacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};
```

- [ ] **Run migration**

```bash
php artisan migrate
```

Expected output: `2026_06_17_000000_create_tarjetas_table .................... 16.80ms DONE`

- [ ] **Commit**

```bash
git add database/migrations/2026_06_17_000000_create_tarjetas_table.php
git commit -m "feat(tarjetas): create tarjetas table"
```

---

### Task 2: Create Tarjeta model

**Files:**
- Create: `app/Models/Tarjeta.php`

- [ ] **Write model**

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Tarjeta extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'codigo_qr',
        'empleado_id',
        'fecha_asignacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $tarjeta) {
            if (! $tarjeta->codigo_qr) {
                $max = static::where('codigo_qr', 'like', 'AG-%')
                    ->max(DB::raw("CAST(SUBSTRING(codigo_qr, 4) AS UNSIGNED)"));
                $tarjeta->codigo_qr = 'AG-'.str_pad((int) $max + 1, 5, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (self $tarjeta) {
            if ($tarjeta->empleado_id && $tarjeta->isDirty('empleado_id')) {
                static::where('empleado_id', $tarjeta->empleado_id)
                    ->where('id', '!=', $tarjeta->id)
                    ->where('activo', true)
                    ->update(['activo' => false, 'fecha_asignacion' => null]);
                $tarjeta->fecha_asignacion = now();
            }
            if (! $tarjeta->empleado_id) {
                $tarjeta->fecha_asignacion = null;
            }
        });
    }
}
```

- [ ] **Commit**

```bash
git add app/Models/Tarjeta.php
git commit -m "feat(tarjetas): add Tarjeta model with QR auto-generation and reassignment logic"
```

---

### Task 3: Add `tarjetas()` relationship to Empleado model

**Files:**
- Modify: `app/Models/Empleado.php`

- [ ] **Add relationship** — after the existing `contratista()` method:

```php
public function tarjetas(): HasMany
{
    return $this->hasMany(Tarjeta::class);
}
```

Insert this before the closing `}` of the class, after `contratista()`.

- [ ] **Commit**

```bash
git add app/Models/Empleado.php
git commit -m "feat(empleados): add tarjetas relationship"
```

---

### Task 4: Register tarjetas entity in MantenedorController

**Files:**
- Modify: `app/Http/Controllers/Mantenedores/MantenedorController.php`

- [ ] **Add `use App\Models\Tarjeta;`** import (alphabetic order, after `SectorRiego`):

```php
use App\Models\SectorRiego;
use App\Models\Tarjeta; // <-- add this
use App\Models\TipoDocumento;
```

- [ ] **Add ENTITIES entry for `tarjetas`** — insert after the `'tractores'` entry, before `'productos-sag'` (around line 302-303):

```php
        'tarjetas' => [
            'page' => 'mantenedores/tarjetas',
            'title' => 'Tarjetas',
            'description' => 'Tarjetas con QR para identificación en faenas',
            'endpoint' => '/mantenedores/tarjetas',
            'model' => Tarjeta::class,
            'rules' => [
                'codigo_qr' => 'required|string|max:20|unique:tarjetas,codigo_qr',
                'empleado_id' => 'nullable|uuid|exists:empleados,id',
                'activo' => 'boolean',
            ],
        ],
```

- [ ] **Add `cards()` dedicated method** — before the closing `}` of the class, after `equiposAplicacion()`:

```php
    public function cards()
    {
        return Inertia::render('mantenedores/tarjetas', [
            'items' => Tarjeta::with('empleado')->orderBy('codigo_qr')->get(),
            'empleados' => Empleado::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'rut']),
        ]);
    }
```

- [ ] **Commit**

```bash
git add app/Http/Controllers/Mantenedores/MantenedorController.php
git commit -m "feat(tarjetas): register entity in MantenedorController"
```

---

### Task 5: Add named route for tarjetas

**Files:**
- Modify: `routes/web.php`

- [ ] **Add route** — after the `'equipos-aplicacion'` route (line 80), before the generic routes:

```php
        Route::get('equipos-aplicacion', [MantenedorController::class, 'equiposAplicacion'])->name('equipos-aplicacion');
        Route::get('tarjetas', [MantenedorController::class, 'cards'])->name('tarjetas'); // <-- add this
```

- [ ] **Commit**

```bash
git add routes/web.php
git commit -m "feat(tarjetas): add named route for tarjetas"
```

---

### Task 6: Create `tarjetas.tsx` frontend page

**Files:**
- Create: `resources/js/pages/mantenedores/tarjetas.tsx`

- [ ] **Write component** (follows `employees.tsx` pattern):

```tsx
import { useMemo } from 'react';
import { Head } from '@inertiajs/react';
import MantenedorListPage from '@/components/mantenedores/MantenedorListPage';
import type { MantenedorConfig, MantenedorField } from '@/components/mantenedores/mantenedor-types';

interface Props {
  items: Record<string, unknown>[];
  empleados: Array<{ id: string; nombre: string; apellido: string; rut: string }>;
}

export default function TarjetasPage({ items, empleados }: Props) {
  const fields: MantenedorField[] = useMemo(() => [
    { name: 'codigo_qr', label: 'Código QR', type: 'text' },
    {
      name: 'empleado_id',
      label: 'Empleado',
      type: 'select',
      options: empleados.map((e) => ({
        value: e.id,
        label: `${e.nombre} ${e.apellido} (${e.rut})`,
      })),
    },
    { name: 'activo', label: 'Activa', type: 'switch' },
  ], [empleados]);

  const config: MantenedorConfig = {
    title: 'Tarjetas',
    description: 'Tarjetas con QR para identificación en faenas',
    endpoint: '/mantenedores/tarjetas',
    fields,
    cardTitle: (item: Record<string, unknown>) => item.codigo_qr as string,
    cardSubtitle: (item: Record<string, unknown>) => {
      const emp = (item as Record<string, unknown>).empleado as Record<string, unknown> | null;
      return emp ? `${emp.nombre} ${emp.apellido}` : 'Sin asignar';
    },
  };

  return (
    <>
      <Head title={config.title} />
      <MantenedorListPage config={config} items={items} />
    </>
  );
}

TarjetasPage.layout = {
  breadcrumbs: [
    { title: 'Mantenedores', href: '/mantenedores' },
    { title: 'Tarjetas', href: '/mantenedores/tarjetas' },
  ],
};
```

- [ ] **Commit**

```bash
git add resources/js/pages/mantenedores/tarjetas.tsx
git commit -m "feat(tarjetas): add frontend mantenedor page"
```

---

### Task 7: Add to sidebar navigation

**Files:**
- Modify: `resources/js/components/SidebarNavigation.tsx`

- [ ] **Add sidebar item** — insert after `{ title: 'Tractores', ... }` (line 80) in alphabetical order:

```tsx
    { title: 'Tarjetas', href: '/mantenedores/tarjetas' },
```

- [ ] **Commit**

```bash
git add resources/js/components/SidebarNavigation.tsx
git commit -m "feat(tarjetas): add sidebar link"
```

---

### Task 8: Run lint and PHP syntax check

- [ ] **PHP syntax check**

```bash
php -l app/Models/Tarjeta.php && php -l app/Models/Empleado.php && php -l app/Http/Controllers/Mantenedores/MantenedorController.php
```

- [ ] **TypeScript lint**

```bash
npx eslint resources/js/pages/mantenedores/tarjetas.tsx resources/js/components/SidebarNavigation.tsx --no-ignore
```

- [ ] **PHP CS fix** (if available)

```bash
composer cs 2>/dev/null || echo "composer cs not configured"
```

- [ ] **Commit**

```bash
git add -A
git commit -m "chore(tarjetas): lint fixes"
```

---

### Task 9: Test end-to-end

- [ ] **Start dev server and verify:**
  1. Navigate to `/mantenedores/tarjetas` — should see empty list with FAB
  2. Click "+" to create → form appears with QR auto-generated, empleado select, activo switch
  3. Fill and save → card created
  4. Assign to an employee → card appears, other active card for same employee is deactivated
  5. Edit → verify QR is read-only on edit
  6. Toggle active/inactive
  7. Delete → soft delete works

```bash
php artisan serve &
npm run dev &
```
