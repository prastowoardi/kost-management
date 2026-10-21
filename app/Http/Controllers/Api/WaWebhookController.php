<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WaWebhookController extends Controller
{
    public function __invoke(Request $request, WhatsAppBotService $bot)
    {
        $key = (string) config('services.whatsapp.webhook_key', '');

        if ($key === '' || ! hash_equals($key, (string) $request->header('X-Webhook-Key'))) {
            Log::warning('Webhook WA rejected: Key is not valid.', ['ip' => $request->ip()]);

            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        try {
            $bot->handle($request->all());

            return response()->json(['status' => 'ok']);
        } catch (Throwable $e) {
            Log::error('Webhook WA error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['status' => 'error'], 500);
        }
    }
}