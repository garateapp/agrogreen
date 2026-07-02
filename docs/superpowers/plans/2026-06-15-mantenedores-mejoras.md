# Mejoras a Mantenedores Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add especie/variedad FK to Cuarteles, bulk upload, multi-select/bulk delete, and dynamic filters to all mantenedores.

**Architecture:** Extend existing MantenedorListPage with selection state, filter bar, and import button. Backend adds generic Import/Export classes driven by entity config. Cuartel migration migrates text fields to FKs.

**Tech Stack:** Laravel 10+, maatwebsite/laravel-excel (to install), React + MUI + Inertia, MySQL.

---

### Task 1: Cuartel migration — add especie_id, variedad_id, migrate data, drop old columns

**Files:**
- Create: `database/migrations/2026_06_15_000061_add_especie_variedad_to_cuartels.php`
- Modify: `app/Models/Cuartel.php`
- Verify: `app/Models/Especie.php`, `app/Models/Variedad.php`

- [ ] **Step 1: Create migration**

```php
<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->foreignUuid('especie_id')->nullable()->constrained('especies')->nullOnDelete()->after('centro_costo_id');
            $table->foreignUuid('variedad_id')->nullable()->constrained('variedades')->nullOnDelete()->after('especie_id');
        });

        // Migrate existing data: match especie_cultivo → especies.nombre, variedad → variedades.nombre
        DB::statement('UPDATE cuartels c JOIN especies e ON e.nombre = c.especie_cultivo SET c.especie_id = e.id');
        DB::statement('UPDATE cuartels c JOIN variedades v ON v.nombre = c.variedad SET c.variedad_id = v.id');

        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropColumn(['especie_cultivo', 'variedad']);
        });
    }

    public function down(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->string('especie_cultivo')->nullable();
            $table->string('variedad')->nullable();
        });

        // Restore text from FK names
        DB::statement('UPDATE cuartels c JOIN especies e ON e.id = c.especie_id SET c.especie_cultivo = e.nombre');
        DB::statement('UPDATE cuartels c JOIN variedades v ON v.id = c.variedad_id SET c.variedad = v.nombre');

        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('especie_id');
            $table->dropConstrainedForeignId('variedad_id');
        });
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: Output shows `2026_06_15_000061_add_especie_variedad_to_cuartels` migrated.

- [ ] **Step 3: Update Cuartel model — add relationships + fillable**

Edit `app/Models/Cuartel.php`:

- Remove `'especie_cultivo'`, `'variedad'` from `$fillable`
- Add `'especie_id'`, `'variedad_id'` to `$fillable`
- Add methods:

```php
public function especie(): BelongsTo
{
    return $this->belongsTo(Especie::class);
}

public function variedad(): BelongsTo
{
    return $this->belongsTo(Variedad::class);
}
```

- [ ] **Step 4: Verify migration + model**

Run: `php artisan tinker --execute="echo \App\Models\Cuartel::with('especie','variedad')->first()?->especie?->nombre ?? 'null' . PHP_EOL;"`
Expected: Prints especie name (or null if no match found).

---

### Task 2: Install maatwebsite/laravel-excel + create import/export classes

**Files:**
- Create: `app/Exports/GenericEntityTemplateExport.php`
- Create: `app/Imports/GenericEntityImport.php`

- [ ] **Step 1: Install package**

```bash
composer require maatwebsite/laravel-excel
```

Expected: Package installed with no errors.

- [ ] **Step 2: Create GenericEntityTemplateExport**

`app/Exports/GenericEntityTemplateExport.php`:

```php
<?php
declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GenericEntityTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private readonly array $fields,
        private readonly string $title,
    ) {}

    /**
     * @return array<int, array>
     */
    public function array(): array
    {
        return []; // empty template, just headers
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        // Use field labels as headers, skip system fields
        $skip = ['id', 'tenant_id', 'created_at', 'updated_at', 'deleted_at'];

        return collect($this->fields)
            ->reject(fn (array $f) => in_array($f['name'], $skip, true))
            ->pluck('label')
            ->toArray();
    }

    public function title(): string
    {
        return $this->title;
    }
}
```

- [ ] **Step 3: Create GenericEntityImport**

`app/Imports/GenericEntityImport.php`:

```php
<?php
declare(strict_types=1);

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class GenericEntityImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    private int $inserted = 0;

    /**
     * @param  class-string  $modelClass
     * @param  array<string, string>  $fieldMap  ['Header Label' => 'column_name']
     * @param  array<string, mixed>  $defaults  Default values (e.g. tenant_id)
     * @param  array<string, mixed>  $rules  Validation rules from entity config
     */
    public function __construct(
        private readonly string $modelClass,
        private readonly array $fieldMap,
        private readonly array $defaults = [],
        private readonly array $rules = [],
        private array $errors = [],
    ) {}

    /**
     * @return array<int, array{row: int, message: string}>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function inserted(): int
    {
        return $this->inserted;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $data = [];

            foreach ($this->fieldMap as $header => $column) {
                $data[$column] = $row[$header] ?? null;
            }

            $data = array_merge($this->defaults, $data);

            // Validate against entity rules
            $validator = Validator::make($data, $this->rules);
            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => implode('; ', $validator->errors()->all()),
                ];
                continue;
            }

            try {
                $this->modelClass::create($data);
                $this->inserted++;
            } catch (\Throwable $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
```

---

### Task 3: Controller methods + routes

**Files:**
- Modify: `app/Http/Controllers/Mantenedores/MantenedorController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Update `paddocks()` method to pass especies/variedades**

In `MantenedorController.php`, modify the `paddocks()` method. Add `'especie', 'variedad'` to the existing `with()` call and append especies/variedades to the response:

```php
// Existing code, add 'especie' and 'variedad' to the with() clause
$cuarteles = Cuartel::query()
    ->with(['centroCosto', 'especie', 'variedad'])
    ->orderBy('nombre')
    ->get();
```

```php
$especies = Especie::orderBy('nombre')->get(['id', 'nombre']);
$variedades = Variedad::orderBy('nombre')->get(['id', 'nombre', 'especie_id']);

return inertia('mantenedores/paddocks', [
    'items' => $cuarteles,
    'especies' => $especies,
    'variedades' => $variedades,
    'centroCostos' => $centroCostos,
]);
```

Add imports: `use App\Models\Especie; use App\Models\Variedad;` at top.

- [ ] **Step 2: Add `exportTemplate()` method**

```php
public function exportTemplate(string $entity): BinaryFileResponse
{
    $config = $this->getEntityConfig($entity);
    $export = new GenericEntityTemplateExport($config['fields'], $config['title']);

    return Excel::download($export, "{$entity}-plantilla.xlsx");
}
```

Add imports: `use App\Exports\GenericEntityTemplateExport; use Maatwebsite\Excel\Facades\Excel; use Symfony\Component\HttpFoundation\BinaryFileResponse;`

- [ ] **Step 3: Add `import()` method**

```php
public function import(string $entity, Request $request): RedirectResponse
{
    $config = $this->getEntityConfig($entity);

    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ]);

    $skip = ['id', 'tenant_id', 'created_at', 'updated_at', 'deleted_at'];
    $fieldMap = collect($config['fields'])
        ->reject(fn (array $f) => in_array($f['name'], $skip, true))
        ->mapWithKeys(fn (array $f) => [$f['label'] => $f['name']])
        ->toArray();

    $import = new GenericEntityImport(
        modelClass: $config['model'],
        fieldMap: $fieldMap,
        defaults: ['tenant_id' => auth()->user()->tenant_id],
        rules: $config['rules'] ?? [],
    );

    Excel::import($import, $request->file('file'));

    $inserted = $import->inserted();
    $errors = $import->errors();
    $message = "{$inserted} registro(s) importado(s)";
    if (!empty($errors)) {
        $message .= ", " . count($errors) . " error(es).";
        return redirect()->back()->with('warning', $message);
    }

    return redirect()->back()->with('success', $message);
}
```

- [ ] **Step 4: Add `batchDestroy()` method**

```php
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
```

- [ ] **Step 5: Add routes**

In `routes/web.php`, inside the `mantenedores.` group, replace the existing generic routes block with this ordering (specific routes before generic `{entity}`):

```php
Route::get('{entity}/template', [MantenedorController::class, 'exportTemplate'])->name('template');
Route::post('{entity}/import', [MantenedorController::class, 'import'])->name('import');
Route::delete('{entity}/batch', [MantenedorController::class, 'batchDestroy'])->name('batch-destroy');
Route::get('{entity}', [MantenedorController::class, 'index'])->name('simple');
Route::post('{entity}', [MantenedorController::class, 'store'])->name('store');
Route::put('{entity}/{id}', [MantenedorController::class, 'update'])->name('update');
Route::delete('{entity}/{id}', [MantenedorController::class, 'destroy'])->name('destroy');
Route::patch('{entity}/{id}/toggle-status', [MantenedorController::class, 'toggleStatus'])->name('toggle-status');
```

This ensures `{entity}/template`, `{entity}/import`, and `{entity}/batch` are matched before the generic `{entity}` or `{entity}/{id}` patterns.

- [ ] **Step 6: Verify ENTITIES constant + add helper method**

The controller already has `const ENTITIES = [...]` with 34 entities. Each entry has `page`, `title`, `description`, `endpoint`, `model`, `rules`. The existing `getModelClass()` helper already resolves the entity key to a model class. Add a new `getEntityConfig()` helper that returns the full entity array for the import/template methods:

```php
private function getEntityConfig(string $entity): array
{
    $entityKey = str_replace('-', '_', $entity);
    
    if (!isset(self::ENTITIES[$entityKey])) {
        abort(404, "Entity '{$entity}' not found.");
    }
    
    return self::ENTITIES[$entityKey];
}
```

---

### Task 4: Frontend mantenedor-types — add selection & filter support

**Files:**
- Modify: `resources/js/components/mantenedores/mantenedor-types.ts`

- [ ] **Step 1: Update types**

Add to the existing types:

```ts
export interface FilterConfig {
  field: MantenedorField;
  value: unknown;
}

export interface MantenedorListHandlers {
  onToggleSelect: (id: string) => void;
  onSelectAll: () => void;
  onClearSelection: () => void;
  onFiltersChange: (filters: Record<string, unknown>) => void;
  onClearFilters: () => void;
  onImport: () => void;
}
```

Add to `MantenedorField` if not already present:
```ts
export interface MantenedorField {
  name: string;
  label: string;
  type: FieldType;
  required?: boolean;
  options?: Array<{ value: string | number | boolean; label: string }>;  // for select fields
  placeholder?: string;
}
```

---

### Task 5: Add checkbox to MantenedorCard

**Files:**
- Modify: `resources/js/components/mantenedores/MantenedorCard.tsx`

- [ ] **Step 1: Add checkbox prop + rendering**

Add `selected?: boolean` and `onToggleSelect?: () => void` to the component props.

In the card's root element, prepend a checkbox:

```tsx
{onToggleSelect && (
  <Checkbox
    checked={selected ?? false}
    onChange={onToggleSelect}
    sx={{
      position: 'absolute',
      top: 4,
      left: 4,
      zIndex: 1,
      opacity: selected ? 1 : 0,
      '&:hover': { opacity: 1 },
      // Show on hover of the card
      '.MuiCard-root:hover &': { opacity: 1 },
    }}
  />
)}
```

Wrap the card in a `Box` with `position: 'relative'`.

---

### Task 6: Create MantenedorBatchToolbar

**Files:**
- Create: `resources/js/components/mantenedores/MantenedorBatchToolbar.tsx`

- [ ] **Step 1: Create toolbar component**

```tsx
import Button from '@mui/material/Button';
import Typography from '@mui/material/Typography';
import Paper from '@mui/material/Paper';
import DeleteIcon from '@mui/icons-material/Delete';
import CloseIcon from '@mui/icons-material/Close';

interface BatchToolbarProps {
  selectedCount: number;
  totalFilteredCount: number;
  onSelectAll: () => void;
  onDeleteSelected: () => void;
  onClearSelection: () => void;
}

export default function MantenedorBatchToolbar({
  selectedCount,
  totalFilteredCount,
  onSelectAll,
  onDeleteSelected,
  onClearSelection,
}: BatchToolbarProps) {
  return (
    <Paper
      elevation={3}
      sx={{
        position: 'fixed',
        bottom: 16,
        left: '50%',
        transform: 'translateX(-50%)',
        zIndex: 1200,
        display: 'flex',
        alignItems: 'center',
        gap: 2,
        px: 3,
        py: 1.5,
        borderRadius: 2,
      }}
    >
      <Typography variant="body2" color="text.secondary">
        <strong>{selectedCount}</strong> seleccionado(s)
      </Typography>
      {selectedCount < totalFilteredCount && (
        <Button size="small" onClick={onSelectAll}>
          Seleccionar todos ({totalFilteredCount})
        </Button>
      )}
      <Button
        size="small"
        color="error"
        startIcon={<DeleteIcon />}
        onClick={onDeleteSelected}
      >
        Eliminar seleccionados
      </Button>
      <Button
        size="small"
        startIcon={<CloseIcon />}
        onClick={onClearSelection}
      >
        Cancelar
      </Button>
    </Paper>
  );
}
```

---

### Task 7: Create MantenedorFilterBar

**Files:**
- Create: `resources/js/components/mantenedores/MantenedorFilterBar.tsx`

- [ ] **Step 1: Create filter bar component**

```tsx
import { useState, useMemo } from 'react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';
import Chip from '@mui/material/Chip';
import Collapse from '@mui/material/Collapse';
import FilterListIcon from '@mui/icons-material/FilterList';
import type { MantenedorField } from './mantenedor-types';

interface FilterBarProps {
  fields: MantenedorField[];
  items: Record<string, unknown>[];
  filters: Record<string, unknown>;
  onFiltersChange: (filters: Record<string, unknown>) => void;
}

export default function MantenedorFilterBar({
  fields,
  items,
  filters,
  onFiltersChange,
}: FilterBarProps) {
  const [open, setOpen] = useState(false);

  const activeCount = useMemo(
    () => Object.values(filters).filter((v) => v !== '' && v !== null && v !== undefined && v !== 'all').length,
    [filters],
  );

  const filterableFields = fields.filter(
    (f) => ['select', 'text', 'number', 'date', 'boolean', 'switch', 'email', 'rut'].includes(f.type),
  );

  const handleChange = (name: string, value: unknown) => {
    onFiltersChange({ ...filters, [name]: value });
  };

  const clearAll = () => onFiltersChange({});

  // Extract unique options from data for select fields
  const getOptions = (field: MantenedorField) => {
    if (field.options) return field.options;
    const values = [...new Set(items.map((item) => item[field.name] as string).filter(Boolean))];
    return values.map((v) => ({ value: v, label: v }));
  };

  return (
    <Box sx={{ mb: 2 }}>
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
        <Button
          size="small"
          startIcon={<FilterListIcon />}
          onClick={() => setOpen(!open)}
          color={activeCount > 0 ? 'primary' : 'inherit'}
        >
          Filtros
          {activeCount > 0 && (
            <Chip
              size="small"
              label={activeCount}
              color="primary"
              sx={{ ml: 0.5, height: 20, minWidth: 20 }}
            />
          )}
        </Button>
        {activeCount > 0 && (
          <Button size="small" onClick={clearAll}>
            Limpiar filtros
          </Button>
        )}
      </Box>

      <Collapse in={open}>
        <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 2, mt: 1 }}>
          {filterableFields.map((field) => {
            if (field.type === 'select' || field.type === 'boolean' || field.type === 'switch') {
              const options = field.type === 'select' ? getOptions(field)
                : [{ value: 'all', label: 'Todos' }, { value: '1', label: 'Sí' }, { value: '0', label: 'No' }];
              return (
                <FormControl key={field.name} size="small" sx={{ minWidth: 160 }}>
                  <InputLabel>{field.label}</InputLabel>
                  <Select
                    value={filters[field.name] ?? 'all'}
                    label={field.label}
                    onChange={(e) => handleChange(field.name, e.target.value === 'all' ? '' : e.target.value)}
                  >
                    <MenuItem value="all">Todos</MenuItem>
                    {options.map((opt) => (
                      <MenuItem key={String(opt.value)} value={String(opt.value)}>
                        {opt.label}
                      </MenuItem>
                    ))}
                  </Select>
                </FormControl>
              );
            }

            if (field.type === 'date') {
              const range = (filters[field.name] as { min?: string; max?: string }) ?? {};
              return (
                <Box key={field.name} sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                  <TextField
                    size="small"
                    label={`${field.label} (desde)`}
                    type="date"
                    InputLabelProps={{ shrink: true }}
                    value={range.min ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, min: e.target.value || undefined })}
                    sx={{ width: 160 }}
                  />
                  <TextField
                    size="small"
                    label={`${field.label} (hasta)`}
                    type="date"
                    InputLabelProps={{ shrink: true }}
                    value={range.max ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, max: e.target.value || undefined })}
                    sx={{ width: 160 }}
                  />
                </Box>
              );
            }

            if (field.type === 'number') {
              const range = (filters[field.name] as { min?: number; max?: number }) ?? {};
              return (
                <Box key={field.name} sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                  <TextField
                    size="small"
                    label={`${field.label} (min)`}
                    type="number"
                    value={range.min ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, min: e.target.value ? Number(e.target.value) : undefined })}
                    sx={{ width: 120 }}
                  />
                  <TextField
                    size="small"
                    label={`${field.label} (max)`}
                    type="number"
                    value={range.max ?? ''}
                    onChange={(e) => handleChange(field.name, { ...range, max: e.target.value ? Number(e.target.value) : undefined })}
                    sx={{ width: 120 }}
                  />
                </Box>
              );
            }

            // text, email, rut, textarea
            return (
              <TextField
                key={field.name}
                size="small"
                label={field.label}
                value={filters[field.name] ?? ''}
                onChange={(e) => handleChange(field.name, e.target.value)}
                sx={{ minWidth: 160 }}
              />
            );
          })}
        </Box>
      </Collapse>
    </Box>
  );
}
```

---

### Task 8: Create MantenedorImportModal

**Files:**
- Create: `resources/js/components/mantenedores/MantenedorImportModal.tsx`

- [ ] **Step 1: Create import modal**

```tsx
import { useState, useRef } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import Typography from '@mui/material/Typography';
import Box from '@mui/material/Box';
import LinearProgress from '@mui/material/LinearProgress';
import Alert from '@mui/material/Alert';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import DownloadIcon from '@mui/icons-material/Download';
import { router } from '@inertiajs/react';

interface ImportModalProps {
  open: boolean;
  onClose: () => void;
  entityEndpoint: string;
  entityTitle: string;
}

function parsePreview(file: File): Promise<{ headers: string[]; rows: string[][] }> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const text = e.target?.result as string;
      const lines = text.split('\n').filter(Boolean);
      if (lines.length === 0) { resolve({ headers: [], rows: [] }); return; }
      const headers = lines[0].split(',').map((h) => h.trim());
      const rows = lines.slice(1, 6).map((line) => line.split(',').map((c) => c.trim()));
      resolve({ headers, rows });
    };
    reader.onerror = () => reject(new Error('Error reading file'));
    reader.readAsText(file.slice(0, 1024 * 50)); // read first 50KB
  });
}

export default function MantenedorImportModal({
  open,
  onClose,
  entityEndpoint,
  entityTitle,
}: ImportModalProps) {
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [result, setResult] = useState<'success' | 'warning' | null>(null);
  const [resultMessage, setResultMessage] = useState('');
  const [preview, setPreview] = useState<{ headers: string[]; rows: string[][] } | null>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  const handleDownloadTemplate = () => {
    window.open(`${entityEndpoint}/template`, '_blank');
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];
    if (f) {
      setFile(f);
      setResult(null);
      setPreview(null);
      if (f.name.endsWith('.csv')) {
        parsePreview(f).then(setPreview).catch(() => {});
      }
    }
  };

  const handleUpload = () => {
    if (!file) return;

    setUploading(true);
    setResult(null);

    const formData = new FormData();
    formData.append('file', file);

    router.post(`${entityEndpoint}/import`, formData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        setUploading(false);
        const flash = page.props.flash as Record<string, string> | undefined;
        if (flash?.success) {
          setResult('success');
          setResultMessage(flash.success);
        } else if (flash?.warning) {
          setResult('warning');
          setResultMessage(flash.warning);
        }
        setFile(null);
      },
      onError: () => {
        setUploading(false);
        setResult('warning');
        setResultMessage('Error al importar el archivo.');
      },
    });
  };

  const handleClose = () => {
    if (!uploading) {
      setFile(null);
      setResult(null);
      setResultMessage('');
      setPreview(null);
      onClose();
    }
  };

  return (
    <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
      <DialogTitle>Importar {entityTitle}</DialogTitle>
      <DialogContent>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Button
            variant="outlined"
            startIcon={<DownloadIcon />}
            onClick={handleDownloadTemplate}
          >
            Descargar plantilla Excel
          </Button>

          <Box
            sx={{
              border: '2px dashed',
              borderColor: file ? 'primary.main' : 'grey.300',
              borderRadius: 1,
              p: 3,
              textAlign: 'center',
              cursor: 'pointer',
            }}
            onClick={() => inputRef.current?.click()}
          >
            <input
              ref={inputRef}
              type="file"
              accept=".xlsx,.xls,.csv"
              hidden
              onChange={handleFileChange}
            />
            <CloudUploadIcon sx={{ fontSize: 40, color: 'grey.400', mb: 1 }} />
            <Typography variant="body2" color="text.secondary">
              {file ? file.name : 'Haga clic para seleccionar un archivo Excel o CSV'}
            </Typography>
          </Box>

          {preview && (
            <Box sx={{ mt: 1 }}>
              <Typography variant="caption" color="text.secondary">
                Vista previa (primeras filas):
              </Typography>
              <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                <thead>
                  <tr>
                    {preview.headers.map((h, i) => (
                      <th key={i} style={{ border: '1px solid #ddd', padding: 4, textAlign: 'left' }}>{h}</th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {preview.rows.map((row, ri) => (
                    <tr key={ri}>
                      {row.map((cell, ci) => (
                        <td key={ci} style={{ border: '1px solid #ddd', padding: 4 }}>{cell}</td>
                      ))}
                    </tr>
                  ))}
                </tbody>
              </table>
            </Box>
          )}

          {uploading && <LinearProgress />}

          {result && (
            <Alert severity={result === 'success' ? 'success' : 'warning'}>
              {resultMessage}
            </Alert>
          )}
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={handleClose} disabled={uploading}>Cancelar</Button>
        <Button
          variant="contained"
          disabled={!file || uploading}
          onClick={handleUpload}
        >
          {uploading ? 'Importando...' : 'Importar'}
        </Button>
      </DialogActions>
    </Dialog>
  );
}
```

---

### Task 9: Wire everything in MantenedorListPage

**Files:**
- Modify: `resources/js/components/mantenedores/MantenedorListPage.tsx`

- [ ] **Step 1: Read current file to understand structure**

Read `resources/js/components/mantenedores/MantenedorListPage.tsx` fully.

- [ ] **Step 2: Add state + handlers for selection, filters, import**

After existing state declarations, add:

```ts
// Selection state
const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());

// Filter state
const [filters, setFilters] = useState<Record<string, unknown>>({});

// Import modal
const [importOpen, setImportOpen] = useState(false);
```

- [ ] **Step 3: Add filtered items computation**

After the items prop, add (before any return):

```ts
// Apply filters
const filteredItems = useMemo(() => {
  return items.filter((item) => {
    // Search text filter (existing — state is `search`)
    if (search) {
      const haystack = JSON.stringify(item).toLowerCase();
      if (!haystack.includes(search.toLowerCase())) return false;
    }

    // Dynamic field filters
    for (const [key, value] of Object.entries(filters)) {
      if (value === '' || value === null || value === undefined) continue;

      if (typeof value === 'object' && 'min' in (value as object)) {
        // Range filter (number or date)
        const range = value as { min?: number | string; max?: number | string };
        const itemVal = item[key];
        if (range.min !== undefined) {
          const min = typeof range.min === 'string' ? new Date(range.min).getTime() : Number(range.min);
          const val = typeof itemVal === 'string' ? new Date(itemVal).getTime() : Number(itemVal);
          if (val < min) return false;
        }
        if (range.max !== undefined) {
          const max = typeof range.max === 'string' ? new Date(range.max).getTime() : Number(range.max);
          const val = typeof itemVal === 'string' ? new Date(itemVal).getTime() : Number(itemVal);
          if (val > max) return false;
        }
      } else if (typeof value === 'string') {
        const itemVal = String(item[key] ?? '').toLowerCase();
        if (!itemVal.includes(value.toLowerCase())) return false;
      } else {
        if (item[key] !== value) return false;
      }
    }
    return true;
  });
}, [items, search, filters]);

// Toggle between activos/todos (existing)
const visibleItems = useMemo(() => {
  if (showAll) return filteredItems;
  return filteredItems.filter((item) => item.activo !== false);
}, [filteredItems, showAll]);
```

- [ ] **Step 4: Add selection handlers**

```ts
const handleToggleSelect = (id: string) => {
  setSelectedIds((prev) => {
    const next = new Set(prev);
    if (next.has(id)) next.delete(id);
    else next.add(id);
    return next;
  });
};

const handleSelectAll = () => {
  const ids = visibleItems.map((item) => item.id as string);
  setSelectedIds(new Set(ids));
};

const handleClearSelection = () => setSelectedIds(new Set());

const handleDeleteSelected = () => {
  if (selectedIds.size === 0) return;
  // Confirm dialog
  if (window.confirm(`¿Eliminar ${selectedIds.size} registro(s)?`)) {
    router.delete(`${config.endpoint}/batch`, {
      data: { ids: Array.from(selectedIds) },
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => setSelectedIds(new Set()),
    });
  }
};
```

- [ ] **Step 5: Replace the items.map() rendering**

Pass selection props to MantenedorCard:

```tsx
<MantenedorCard
  key={item.id}
  item={item}
  config={config}
  selected={selectedIds.has(item.id as string)}
  onToggleSelect={() => handleToggleSelect(item.id as string)}
  onEdit={() => handleEdit(item)}
  onDelete={() => handleDelete(item)}
  onToggleStatus={item.activo !== undefined ? () => handleToggle(item) : undefined}
/>
```

- [ ] **Step 6: Add filter bar + import button + batch toolbar**

In the return JSX, after the MantenedorBar and before the items list:

```tsx
<MantenedorFilterBar
  fields={config.fields}
  items={items}
  filters={filters}
  onFiltersChange={setFilters}
/>

{/* items list */}
<Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
  {visibleItems.map((item) => ...)}
</Box>

{selectedIds.size > 0 && (
  <MantenedorBatchToolbar
    selectedCount={selectedIds.size}
    totalFilteredCount={visibleItems.length}
    onSelectAll={handleSelectAll}
    onDeleteSelected={handleDeleteSelected}
    onClearSelection={handleClearSelection}
  />
)}

<MantenedorImportModal
  open={importOpen}
  onClose={() => setImportOpen(false)}
  entityEndpoint={config.endpoint}
  entityTitle={config.title}
/>
```

In `MantenedorBar.tsx`, add an "Importar" button before the FAB slot (or in the `action` prop if it exists). Wire it via an `onImport` prop:

```tsx
// In MantenedorBar.tsx, add to the props interface
interface MantenedorBarProps {
  title: string;
  search: string;
  onSearchChange: (value: string) => void;
  showAll: boolean;
  onToggleShowAll: () => void;
  onImport?: () => void;
}

// In the JSX, add the import button before the FAB
{onImport && (
  <Button size="small" startIcon={<CloudUploadIcon />} onClick={onImport}>
    Importar
  </Button>
)}
```

In `MantenedorListPage.tsx`, pass `onImport={() => setImportOpen(true)}` to MantenedorBar.

- [ ] **Step 7: Add imports**

```ts
import MantenedorFilterBar from './MantenedorFilterBar';
import MantenedorBatchToolbar from './MantenedorBatchToolbar';
import MantenedorImportModal from './MantenedorImportModal';
```

---

### Task 10: Update paddocks.tsx fields for especie/variedad selects

**Files:**
- Modify: `resources/js/pages/mantenedores/paddocks.tsx`

- [ ] **Step 1: Read current file**

Read `resources/js/pages/mantenedores/paddocks.tsx` to see the current fields.

- [ ] **Step 2: Update fields array**

Replace the old `especie_cultivo` (text) and `variedad` (text) fields with select fields:

```ts
const fields: MantenedorField[] = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true },
  { name: 'centro_costo_id', label: 'Centro de Costo', type: 'select', required: true },
  { name: 'especie_id', label: 'Especie', type: 'select' },
  { name: 'variedad_id', label: 'Variedad', type: 'select' },
  { name: 'superficie_hectareas', label: 'Superficie (ha)', type: 'number', required: true },
  { name: 'ano_plantacion', label: 'Año Plantación', type: 'number', required: true },
  { name: 'distancia_sobre_hilera', label: 'Dist. Sobre Hilera (m)', type: 'number' },
  { name: 'distancia_intra_hilera', label: 'Dist. Intra Hilera (m)', type: 'number' },
];
```

- [ ] **Step 3: Update Props interface**

```ts
interface Props {
  items: Cuartel[];
  especies: Array<{ id: string; nombre: string }>;
  variedades: Array<{ id: string; nombre: string; especie_id: string }>;
  centroCostos: Array<{ id: string; nombre: string }>;
}
```

- [ ] **Step 4: Pass options to fields**

Before the config, map the props to field options:

```ts
// Populate field options from data
const fieldsWithOptions = useMemo(() => fields.map((f) => {
  if (f.name === 'centro_costo_id') {
    return { ...f, options: centroCostos.map((cc) => ({ value: cc.id, label: cc.nombre })) };
  }
  if (f.name === 'especie_id') {
    return { ...f, options: especies.map((e) => ({ value: e.id, label: e.nombre })) };
  }
  if (f.name === 'variedad_id') {
    return { ...f, options: variedades.map((v) => ({ value: v.id, label: v.nombre })) };
  }
  return f;
}), [especies, variedades, centroCostos]);
```

- [ ] **Step 5: Add cascading for variedad in the edit form**

The variedad field needs to filter by selected especie_id. This requires modifying `MantenedorFormModal.tsx` to support cascading selects. Add a `cascadeParent` property to `MantenedorField`:

```ts
export interface MantenedorField {
  // ... existing properties
  cascadeParent?: string;  // name of the parent field (e.g. 'especie_id' for variedad)
}
```

In the paddocks fields:
```ts
{ name: 'variedad_id', label: 'Variedad', type: 'select', cascadeParent: 'especie_id' },
```

In `MantenedorFieldFactory.tsx` (or `MantenedorFormModal.tsx`), when rendering a select with `cascadeParent`, filter its options based on the current value of the parent field:

```ts
const parentValue = formData[cascadeParent];
const filteredOptions = cascadeParent && parentValue
  ? options.filter((opt: any) => opt.especie_id === parentValue || opt.especieId === parentValue)
  : options;
```

- [ ] **Step 6: Update MantenedorFieldFactory.tsx for cascading selects**

Read the file first, then add the cascading filter logic.

---

### Task 11: Run build + lint to verify

**Files:**
- (none, verification only)

- [ ] **Step 1: Build frontend**

Run: `npm run build`
Expected: Build succeeds (watch for TypeScript errors).

- [ ] **Step 2: Run PHP lint**

Run: `composer cs`
Expected: No style violations. If issues, auto-fix with `composer cs-fix`.

- [ ] **Step 3: Test migration rollback**

Run: `php artisan migrate:rollback --step=1`
Expected: Cuartel migration rolls back, old columns restored.

- [ ] **Step 4: Re-run migration**

Run: `php artisan migrate`
Expected: Migration applies cleanly.

---

### Files Created (new)

| File | Purpose |
|---|---|
| `database/migrations/2026_06_15_000061_add_especie_variedad_to_cuartels.php` | Add FK columns, migrate data, drop old |
| `app/Exports/GenericEntityTemplateExport.php` | Excel template download |
| `app/Imports/GenericEntityImport.php` | Generic Excel/CSV import |
| `resources/js/components/mantenedores/MantenedorBatchToolbar.tsx` | Batch actions toolbar |
| `resources/js/components/mantenedores/MantenedorFilterBar.tsx` | Dynamic filter controls |
| `resources/js/components/mantenedores/MantenedorImportModal.tsx` | Import modal with template download + upload |

### Files Modified

| File | Changes |
|---|---|
| `app/Models/Cuartel.php` | +especie/variedad relationships, -old text fillable |
| `app/Http/Controllers/Mantenedores/MantenedorController.php` | +exportTemplate, +import, +batchDestroy; update paddocks() |
| `routes/web.php` | +template, +import, +batch-destroy routes |
| `resources/js/components/mantenedores/mantenedor-types.ts` | +cascadeParent in MantenedorField, filter types |
| `resources/js/components/mantenedores/MantenedorCard.tsx` | +checkbox with selected/onToggleSelect props |
| `resources/js/components/mantenedores/MantenedorListPage.tsx` | +selection state, filters, import button, batch toolbar |
| `resources/js/components/mantenedores/MantenedorBar.tsx` | +onImport prop, +Importar button |
| `resources/js/components/mantenedores/MantenedorFieldFactory.tsx` | +cascadeParent filter for select options |
| `resources/js/pages/mantenedores/paddocks.tsx` | Replace text fields with select, pass options |
