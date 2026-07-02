<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsumoMaquinaria extends Model
{
    use BelongsToTenant, HasUuids, Auditable;

    protected $table = 'consumos_maquinaria';

    protected $fillable = [
        'tenant_id',
        'uso_maquinaria_id',
        'producto_id',
        'cantidad_litros',
        'costo_total_moneda_base',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_litros' => 'decimal:2',
            'costo_total_moneda_base' => 'decimal:2',
        ];
    }

    public function usoMaquinaria(): BelongsTo
    {
        return $this->belongsTo(UsoMaquinaria::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
