<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->audit('created'));
        static::updating(fn ($model) => $model->audit('updated'));
        static::deleted(fn ($model) => $model->audit('deleted'));
    }

    public function audit(string $event): void
    {
        $user = auth()->user();

        $oldValues = null;
        $newValues = null;

        if ($event === 'created') {
            $newValues = $this->auditableValues($this->getAttributes());
        } elseif ($event === 'updated') {
            $dirty = $this->getDirty();
            if (empty($dirty)) {
                return;
            }
            $original = $this->getOriginal();
            $oldValues = [];
            $newValues = [];
            foreach ($dirty as $key => $newVal) {
                if (in_array($key, ['updated_at', 'created_at'], true)) {
                    continue;
                }
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $newVal;
            }
            $oldValues = $this->auditableValues($oldValues);
            $newValues = $this->auditableValues($newValues);
        } elseif ($event === 'deleted') {
            $oldValues = $this->auditableValues($this->getAttributes());
        }

        AuditLog::create([
            'tenant_id' => $user?->tenant_id ?? $this->tenant_id ?? null,
            'user_id' => $user?->id,
            'event' => $event,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'auditable_label' => $this->auditableLabel(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    protected function auditableValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return collect($values)->except([
            'id', 'tenant_id', 'created_at', 'updated_at', 'deleted_at',
            'remember_token',
        ])->toArray();
    }

    public function auditableLabel(): string
    {
        $label = $this->nombre
            ?? $this->name
            ?? $this->titulo
            ?? $this->title
            ?? $this->descripcion
            ?? $this->description
            ?? $this->codigo
            ?? $this->numero_oc
            ?? $this->id;

        return sprintf('%s #%s', class_basename(static::class), $label);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
