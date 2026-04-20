<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrAdminController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ════════════════════════════════════════════════════════════════════
// CREAR ORDEN - CA2 + CA5 (Token único + Timestamp)
// Requiere: Sesión autenticada como gerente local
// ════════════════════════════════════════════════════════════════════
Route::middleware('auth:web')->post('/orders', [OrderController::class, 'store'])->name('api.orders.store');

// QR Validation - Endpoint para validar órdenes con QR
Route::get('/orders/validate', function (Request $request) {
    try {
        $key = $request->query('key');
        
        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'QR key is required',
            ], 400);
        }

        $qrSetting = \App\Models\QrSetting::where('qr_key', $key)
            ->where('is_active', true)
            ->first();

        if (!$qrSetting) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive QR code',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'QR code is valid',
            'qr_key' => $qrSetting->qr_key,
            'is_active' => $qrSetting->is_active,
            'created_at' => $qrSetting->created_at,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error validating QR: ' . $e->getMessage(),
        ], 500);
    }
})->name('api.orders.validate');
