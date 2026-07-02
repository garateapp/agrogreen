<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationContainerDisposal extends Model
{
    use HasUuids, Auditable;

    protected $table = 'application_container_disposals';

    protected $fillable = [
        'application_record_id',
        'producto_sag_id',
        'envases_usados',
        'capacidad_envase',
        'triple_lavado',
        'almacenamiento_temporal',
        'metodo_disposicion',
        'documento_respaldo_url',
    ];

    protected function casts(): array
    {
        return [
            'envases_usados' => 'integer',
            'capacidad_envase' => 'decimal:2',
            'triple_lavado' => 'boolean',
        ];
    }

    public function applicationRecord(): BelongsTo
    {
        return $this->belongsTo(ApplicationRecord::class, 'application_record_id');
    }

    public function productoSAG(): BelongsTo
    {
        return $this->belongsTo(ProductoSAG::class, 'producto_sag_id');
    }
}
