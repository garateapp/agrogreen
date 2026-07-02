<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationRecordProducto extends Model
{
    use HasUuids, Auditable;

    protected $table = 'application_record_productos';

    protected $fillable = [
        'application_record_id',
        'producto_sag_id',
        'lote_id',
        'dosis',
        'unidad_dosis',
        'cantidad_total',
        'volumen_agua',
        'label_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'dosis' => 'decimal:4',
            'cantidad_total' => 'decimal:4',
            'volumen_agua' => 'decimal:2',
            'label_snapshot' => 'json',
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

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
