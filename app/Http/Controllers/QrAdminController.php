<?php

namespace App\Http\Controllers;

use App\Models\QrSetting;
use App\Models\QrGenerationLog;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Str;

class QrAdminController extends Controller
{
    /**
     * CA1: Mostrar el QR actual y la interfaz de gestión
     */
    public function index()
    {
        $qrSetting = QrSetting::getActiveQr();
        
        // Si no existe un QR, crear uno por defecto
        if (!$qrSetting) {
            $qrSetting = $this->generateNewQr();
        }

        // Obtener últimos 10 logs
        $logs = QrGenerationLog::where('qr_setting_id', $qrSetting->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Generar la imagen del QR como base64
        $qrImage = $this->generateQrImage($qrSetting->qr_url);

        return view('admin.qr.index', [
            'qrSetting' => $qrSetting,
            'qrImage' => $qrImage,
            'logs' => $logs,
        ]);
    }

    /**
     * CA2: Generar o actualizar la clave del QR
     */
    public function generate(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Obtener el QR actual
            $currentQr = QrSetting::getActiveQr();
            $oldKey = $currentQr?->qr_key;

            // Generar nueva clave
            $newKey = QrSetting::generateNewKey();
            $qrUrl = route('api.orders.validate', ['key' => $newKey], false);
            $qrUrl = url($qrUrl);

            if ($currentQr) {
                // Actualizar el QR existente
                $currentQr->update([
                    'qr_key' => $newKey,
                    'qr_url' => $qrUrl,
                    'generated_by' => $user->user_id,
                ]);
                $action = 'update';
            } else {
                // Crear uno nuevo
                $currentQr = QrSetting::create([
                    'qr_key' => $newKey,
                    'qr_url' => $qrUrl,
                    'is_active' => true,
                    'generated_by' => $user->user_id,
                ]);
                $action = 'generate';
            }

            // CA5: Registrar en logs
            QrGenerationLog::create([
                'qr_setting_id' => $currentQr->id,
                'action' => $action,
                'old_key' => $oldKey,
                'new_key' => $newKey,
                'admin_id' => $user->user_id,
                'admin_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('admin.qr.index')
                ->with('success', '✓ QR generado exitosamente. Clave: ' . $newKey);
        } catch (\Exception $e) {
            return redirect()->route('admin.qr.index')
                ->with('error', 'Error al generar el QR: ' . $e->getMessage());
        }
    }

    /**
     * CA3: Descargar el QR como imagen PNG
     */
    public function download(Request $request)
    {
        try {
            $qrSetting = QrSetting::getActiveQr();

            if (!$qrSetting) {
                return redirect()->route('admin.qr.index')
                    ->with('error', 'No hay QR disponible para descargar.');
            }

            // CA5: Registrar descarga en logs
            QrGenerationLog::create([
                'qr_setting_id' => $qrSetting->id,
                'action' => 'download',
                'new_key' => $qrSetting->qr_key,
                'admin_id' => auth()->user()->user_id,
                'admin_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Generar la imagen PNG
            $qrCode = new QrCode($qrSetting->qr_url);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Retornar como descarga
            return response($result->getString(), 200)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="qr-validacion-' . date('Y-m-d-His') . '.png"');
        } catch (\Exception $e) {
            return redirect()->route('admin.qr.index')
                ->with('error', 'Error al descargar el QR: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial completo de logs
     */
    public function logs()
    {
        $qrSetting = QrSetting::getActiveQr();
        
        $logs = QrGenerationLog::where('qr_setting_id', $qrSetting->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.qr.logs', [
            'qrSetting' => $qrSetting,
            'logs' => $logs,
        ]);
    }

    /**
     * Método auxiliar: Generar nueva clave y crear QR
     */
    private function generateNewQr()
    {
        $key = QrSetting::generateNewKey();
        $qrUrl = route('api.orders.validate', ['key' => $key], false);
        $qrUrl = url($qrUrl);

        $qrSetting = QrSetting::create([
            'qr_key' => $key,
            'qr_url' => $qrUrl,
            'is_active' => true,
            'generated_by' => auth()->user()->user_id,
        ]);

        QrGenerationLog::create([
            'qr_setting_id' => $qrSetting->id,
            'action' => 'generate',
            'new_key' => $key,
            'admin_id' => auth()->user()->user_id,
            'admin_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $qrSetting;
    }

    /**
     * Método auxiliar: Generar imagen del QR como SVG
     */
    private function generateQrImage($qrUrl)
    {
        try {
            $qrCode = new QrCode($qrUrl);
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);

            return $result->getString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
