<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especie extends Model
{
    use BelongsToTenant, HasUuids, SoftDeletes, Auditable;

    protected $fillable = ['tenant_id', 'familia_id', 'nombre', 'descripcion'];

    public function familia()
    {
        return $this->belongsTo(Familia::class);
    }

    public function variedades()
    {
        return $this->hasMany(Variedad::class);
    }
}
