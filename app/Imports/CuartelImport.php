<?php
declare(strict_types=1);

namespace App\Imports;

use App\Models\CentroCosto;
use App\Models\Cuartel;
use App\Models\Especie;
use App\Models\Variedad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CuartelImport implements ToCollection, WithHeadingRow
{
    private int $inserted = 0;
    private array $errors = [];
    private array $centroCostoCache = [];
    private array $especieCache = [];
    private array $variedadCache = [];

    public function __construct(
        private readonly string $tenantId,
    ) {}

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
            $nombre = trim($row['nombre'] ?? '');
            $especieNombre = trim($row['especie'] ?? '');
            $variedadNombre = trim($row['variedad'] ?? '');
            $cantidadPlantas = $row['cantidad_plantas'] ?? null;
            $superficie = $row['superficie_hectareas'] ?? null;
            $anio = $row['ano_plantacion'] ?? null;

            if (empty($nombre)) {
                $this->errors[] = ['row' => $index + 2, 'message' => 'El nombre del cuartel es obligatorio.'];
                continue;
            }

            if (empty($especieNombre)) {
                $this->errors[] = ['row' => $index + 2, 'message' => 'La especie es obligatoria.'];
                continue;
            }

            // Resolve especie by name
            if (!isset($this->especieCache[$especieNombre])) {
                $especie = Especie::where('nombre', $especieNombre)->first();
                if (!$especie) {
                    $this->errors[] = ['row' => $index + 2, 'message' => "Especie '{$especieNombre}' no encontrada."];
                    continue;
                }
                $this->especieCache[$especieNombre] = $especie->id;
            }
            $especieId = $this->especieCache[$especieNombre];

            // Resolve variedad by name (only within resolved especie)
            $variedadId = null;
            if (!empty($variedadNombre)) {
                $cacheKey = $especieNombre . '::' . $variedadNombre;
                if (!isset($this->variedadCache[$cacheKey])) {
                    $variedad = Variedad::where('nombre', $variedadNombre)
                        ->where('especie_id', $especieId)
                        ->first();
                    if (!$variedad) {
                        $this->errors[] = ['row' => $index + 2, 'message' => "Variedad '{$variedadNombre}' no encontrada para la especie '{$especieNombre}'."];
                        continue;
                    }
                    $this->variedadCache[$cacheKey] = $variedad->id;
                }
                $variedadId = $this->variedadCache[$cacheKey];
            }

            // Resolve centro_costo by name
            $centroCostoId = $cuartel->centro_costo_id ?? null;
            $centroCostoNombre = trim($row['centro_costo'] ?? '');
            if (!empty($centroCostoNombre)) {
                if (!isset($this->centroCostoCache[$centroCostoNombre])) {
                    $cc = CentroCosto::where('nombre', $centroCostoNombre)->first();
                    if (!$cc) {
                        $this->errors[] = ['row' => $index + 2, 'message' => "Centro de costo '{$centroCostoNombre}' no encontrado."];
                        continue;
                    }
                    $this->centroCostoCache[$centroCostoNombre] = $cc->id;
                }
                $centroCostoId = $this->centroCostoCache[$centroCostoNombre];
            }

            // Find or create cuartel by nombre + tenant_id
            $cuartel = Cuartel::firstOrNew([
                'nombre' => $nombre,
                'tenant_id' => $this->tenantId,
            ]);

            $cuartel->fill([
                'tenant_id' => $this->tenantId,
                'especie_id' => $especieId,
                'superficie_hectareas' => $superficie,
                'ano_plantacion' => $anio,
                'centro_costo_id' => $centroCostoId,
                'distancia_sobre_hilera' => $row['distancia_sobre_hilera'] ?? $cuartel->distancia_sobre_hilera,
                'distancia_intra_hilera' => $row['distancia_intra_hilera'] ?? $cuartel->distancia_intra_hilera,
            ]);

            $cuartel->save();

            // Sync variedad pivot
            if ($variedadId && $cantidadPlantas !== null && $cantidadPlantas !== '') {
                $existing = $cuartel->variedades()->where('variedad_id', $variedadId)->first();
                if ($existing) {
                    $cuartel->variedades()->updateExistingPivot($variedadId, [
                        'cantidad_plantas' => (int) $cantidadPlantas,
                    ]);
                } else {
                    $cuartel->variedades()->attach($variedadId, [
                        'cantidad_plantas' => (int) $cantidadPlantas,
                    ]);
                }
            }

            $this->inserted++;
        }
    }

}
