<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
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

        $perPage = min((int) $request->get('per_page', 20), 100);
        $logs = $query->latest()->paginate($perPage);

        foreach ($logs as $log) {
            $payload = $log->payload;
            if (is_array($payload) && ! empty($payload['error_trace'])) {
                unset($payload['error_trace']);
                $log->setAttribute('payload', $payload);
                $log->setAttribute('has_trace', true);
            }
        }

        $stats = $this->aggregate();

        return response()->json([
            'status' => 'success',
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
            'stats' => [
                'total' => $stats->sum('total'),
                'failed' => $stats->filter(fn ($s) => str_contains($s->action, 'FAILED'))->sum('total'),
                'today' => $stats->sum('today'),
            ],
            'actions' => $stats->pluck('action')->sort()->values(),
        ]);
    }

    public function show($id)
    {
        $log = ActivityLog::with('user:id,name')->find($id);

        if (! $log) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $log,
        ]);
    }

    public function stats()
    {
        $stats = $this->aggregate();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $stats->sum('total'),
                'failed' => $stats->filter(fn ($s) => str_contains($s->action, 'FAILED'))->sum('total'),
                'today' => $stats->sum('today'),
                'actions' => $stats->pluck('action')->sort()->values(),
            ],
        ]);
    }

    /**
     * Satu query GROUP BY -> total, failed, today, dan daftar action sekaligus.
     */
    private function aggregate()
    {
        return ActivityLog::selectRaw('action, COUNT(*) as total, SUM(created_at >= ?) as today', [
            now()->startOfDay(),
        ])->groupBy('action')->get();
    }
}
