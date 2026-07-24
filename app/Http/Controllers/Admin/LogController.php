<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
        }

        if ($request->filled('source')) {
            $query->where('payload->source', $request->source);
        }

        $logs = $query->paginate(50);

        $totalLogs = ActivityLog::count();
        $failedLogs = ActivityLog::where('action', 'like', '%FAILED%')->count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.logs.index', compact('logs', 'totalLogs', 'failedLogs', 'todayLogs', 'actions'));
    }
}
