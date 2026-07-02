<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->where('tenant_id', auth()->user()->tenant_id);

        if ($event = $request->get('event')) {
            $query->where('event', $event);
        }
        if ($auditableType = $request->get('auditable_type')) {
            $query->where('auditable_type', $auditableType);
        }
        if ($search = $request->get('search')) {
            $query->where('auditable_label', 'like', "%{$search}%");
        }
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = min((int) $request->get('per_page', 50), 200);
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(fn ($log) => [
                'id' => $log->id,
                'event' => $log->event,
                'auditable_type' => class_basename($log->auditable_type),
                'auditable_label' => $log->auditable_label,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'user_name' => $log->user?->name ?? '—',
                'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
            ]);

        $modelTypes = AuditLog::where('tenant_id', auth()->user()->tenant_id)
            ->distinct('auditable_type')
            ->pluck('auditable_type')
            ->map(fn ($t) => ['value' => $t, 'label' => class_basename($t)])
            ->sortBy('label')
            ->values();

        return Inertia::render('audit/index', [
            'pageTitle' => 'Auditoría',
            'logs' => $this->paginated($logs),
            'filters' => $request->only(['event', 'auditable_type', 'search', 'from', 'to']),
            'modelTypes' => $modelTypes,
        ]);
    }

    protected function paginated($paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}
