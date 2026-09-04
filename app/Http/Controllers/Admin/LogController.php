<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
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
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        $totalLogs = ActivityLog::count();
        $failedLogs = ActivityLog::where('action', 'like', '%FAILED%')->count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $users = User::whereHas('activityLogs')->orderBy('name')->get(['id', 'name']);

        return view('admin.logs.index', compact('logs', 'totalLogs', 'failedLogs', 'todayLogs', 'actions', 'users'));
    }
}
