<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $logs = $query->latest()->paginate($perPage);

        $totalLogs = ActivityLog::count();
        $failedLogs = ActivityLog::where('action', 'like', '%FAILED%')->count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

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
                'total' => $totalLogs,
                'failed' => $failedLogs,
                'today' => $todayLogs,
            ],
            'actions' => $actions,
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
        $total = ActivityLog::count();
        $failed = ActivityLog::where('action', 'like', '%FAILED%')->count();
        $today = ActivityLog::whereDate('created_at', today())->count();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'failed' => $failed,
                'today' => $today,
                'actions' => $actions,
            ],
        ]);
    }
}
