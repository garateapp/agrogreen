<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SyncAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'records' => 'required|array|min:1',
            'records.*.codigo_qr' => 'required|string|max:20',
            'records.*.actividad_id' => 'required|uuid|exists:actividades,id',
            'records.*.fecha' => 'required|date',
            'records.*.cuarteles_ids' => 'nullable|array',
            'records.*.cuarteles_ids.*' => 'uuid|exists:cuartels,id',
            'records.*.sync_id' => 'nullable|string|max:36',
        ];
    }
}
