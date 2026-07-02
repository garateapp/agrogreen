<?php

declare(strict_types=1);

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuartelVariedad extends Model
{
    use HasUuids, Auditable;

    protected $table = 'cuartel_variedad';

    public $incrementing = false;


    protected $primaryKey = null;

    protected $fillable = [
        'cuartel_id',
        'variedad_id',
        'cantidad_plantas',
    ];

    public function cuartel(): BelongsTo
    {
        return $this->belongsTo(Cuartel::class);
    }

    public function variedad(): BelongsTo
    {
        return $this->belongsTo(Variedad::class);
    }
}
