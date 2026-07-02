<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasUuids, Auditable;

    protected $table = 'pagos';

    protected $fillable = [
        'egreso_id',
        'fecha_pago',
        'monto_moneda_base',
        'metodo_pago',
        'cuenta_bancaria_origen',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'date',
            'monto_moneda_base' => 'decimal:2',
        ];
    }

    public function egreso(): BelongsTo
    {
        return $this->belongsTo(Egreso::class, 'egreso_id');
    }
}
