<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%'.$search.'%')
                    ->orWhere('action', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('status')) {
            if ($request->status === 'error') {
                $query->where('action', 'like', '%FAILED%');
            }
        }

        if ($request->filled('source')) {
            $query->where('payload->source', $request->source);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $logs = $query->paginate(20)->withQueryString();

        // Stack trace bisa belasan KB per baris: jangan ikut dikirim ke browser,
        // ambil on-demand lewat endpoint trace() saat user menekan "Lihat trace".
        foreach ($logs as $log) {
            $payload = $log->payload;
            if (is_array($payload) && ! empty($payload['error_trace'])) {
                unset($payload['error_trace']);
                $log->setAttribute('payload', $payload);
                $log->setAttribute('has_trace', true);
            }
        }

        // Satu query GROUP BY -> actions (dropdown) + total/failed/today (3 kartu stats)
        $stats = ActivityLog::selectRaw('action, COUNT(*) as total, SUM(created_at >= ?) as today', [
            now()->startOfDay(),
        ])->groupBy('action')->get();

        $actions = $stats->pluck('action')->sort()->values();
        $totalLogs = (int) $stats->sum('total');
        $failedLogs = (int) $stats->filter(fn ($s) => str_contains($s->action, 'FAILED'))->sum('total');
        $todayLogs = (int) $stats->sum('today');
        $users = User::whereHas('activityLogs')->orderBy('name')->get(['id', 'name']);

        return view('admin.logs.index', compact('logs', 'totalLogs', 'failedLogs', 'todayLogs', 'actions', 'users'));
    }

    public function trace(ActivityLog $log)
    {
        return response()->json([
            'trace' => $log->payload['error_trace'] ?? null,
        ]);
    }
}
