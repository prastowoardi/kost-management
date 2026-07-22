<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        $logs = $query->paginate(50);

        $laravelLog = null;
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $lines = file($logPath);
            $laravelLog = array_slice($lines, -100);
            $laravelLog = array_reverse($laravelLog);
        }

        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.logs.index', compact('logs', 'laravelLog', 'actions'));
    }
}
