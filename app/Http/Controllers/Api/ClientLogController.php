<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required|in:info,warning,error,debug',
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array',
            'screen' => 'nullable|string|max:255',
        ]);

        $action = 'CLIENT_'.strtoupper($request->level);
        $description = $request->message;

        $user = $request->user();
        $payload = array_filter([
            'level' => $request->level,
            'screen' => $request->screen,
            'client_context' => $request->context,
            'user_id' => $user?->id,
        ]);

        LogHelper::log($action, $description, null, $payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Log recorded',
        ]);
    }
}
