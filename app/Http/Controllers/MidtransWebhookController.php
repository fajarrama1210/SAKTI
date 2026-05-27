<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Endpoint: POST /api/midtrans/notification
     * Menerima notifikasi webhook dari server Midtrans.
     * Route ini DIKECUALIKAN dari CSRF protection (lihat bootstrap/app.php).
     */
    public function handle(Request $request)
    {
        Log::info('[MidtransWebhook] Incoming notification', [
            'ip'      => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        $result = $this->midtransService->handleWebhook();

        if ($result['status']) {
            return response()->json(['message' => $result['message']], 200);
        }

        // Kembalikan 200 meski gagal agar Midtrans tidak terus-menerus retry
        // Error sudah dicatat di Log
        return response()->json(['message' => $result['message']], 200);
    }
}
