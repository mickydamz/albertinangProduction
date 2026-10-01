<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $event = $request->get('event', '');
        $model = $request->get('model', '');   // class basename, e.g. "Order"
        $search = $request->get('search', '');

        $logs = AuditLog::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
            ->when($event, fn ($q) => $q->where('event', $event))
            ->when($model, fn ($q) => $q->where('auditable_type', 'App\\Models\\' . $model))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                      ->orWhere('auditable_type', 'like', "%{$search}%")
                      ->orWhere('auditable_id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Distinct model types present, for the filter dropdown
        $modelTypes = AuditLog::query()
            ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
            ->select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn ($t) => class_basename($t))
            ->sort()
            ->values();

        return view('admin.audit.index', compact('logs', 'event', 'model', 'search', 'modelTypes'));
    }
}