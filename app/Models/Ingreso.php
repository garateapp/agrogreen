<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingreso extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $table = 'ingresos';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'tipo_documento',
        'folio_documento',
        'fecha_emision',
        'moneda',
        'tipo_cambio',
        'monto_neto',
        'iva',
        'monto_total',
        'estado',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'tipo_cambio' => 'decimal:4',
            'monto_neto' => 'decimal:2',
            'iva' => 'decimal:2',
            'monto_total' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
