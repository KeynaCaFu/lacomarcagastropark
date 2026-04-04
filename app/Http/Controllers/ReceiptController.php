<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Receipt;
use App\Mail\ReceiptMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Generar y guardar comprobante PDF
     */
    public function generateReceipt(Order $order, $paymentMethod, $receiptReference)
    {
        try {
            // Validar que la orden exista
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Orden no encontrada'
                ];
            }

            // Cargar relaciones necesarias
            $order->load(['items.product', 'user', 'local']);

            // Crear carpeta si no existe
            $receiptDir = public_path('images/orders/comprobantes');
            if (!is_dir($receiptDir)) {
                mkdir($receiptDir, 0777, true);
            }

            // Generar número único de comprobante
            $receiptNumber = $this->generateReceiptNumber();
            $reference = $this->generateReference();

            // Preparar datos para la vista PDF
            $data = [
                'order' => $order,
                'receiptNumber' => $receiptNumber,
                'paymentMethod' => $paymentMethod,
                'receiptReference' => $receiptReference,
                'reference' => $reference,
                'generatedAt' => now(),
            ];

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('receipts.receipt-pdf', $data);
            
            // Nombre del archivo
            $filename = 'comprobante_' . $order->order_number . '_' . $reference . '.pdf';
            $filepath = $receiptDir . '/' . $filename;
            $pdfPath = 'images/orders/comprobantes/' . $filename;

            // Guardar PDF
            $pdf->save($filepath);

            // Crear registro en tbreceipt
            $receipt = Receipt::create([
                'order_id' => $order->order_id,
                'receipt_number' => $receiptNumber,
                'payment_method' => $paymentMethod,
                'receipt_reference' => $receiptReference,
                'pdf_path' => $pdfPath,
                'sent_to_email' => false,
            ]);

            // Actualizar orden con voucher_path para compatibilidad
            $order->update([
                'voucher_path' => $pdfPath,
            ]);

            // Enviar email si el cliente tiene email
            if ($order->user && $order->user->first() && $order->user->first()->email) {
                $this->sendReceiptEmail($order, $receipt);
            }

            return [
                'success' => true,
                'message' => 'Comprobante generado exitosamente',
                'pdf_path' => $pdfPath,
                'receipt_number' => $receiptNumber,
                'receipt_id' => $receipt->receipt_id,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al generar comprobante: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Descargar comprobante PDF
     */
    public function downloadReceipt(Order $order)
    {
        try {
            // Obtener el recibo más reciente de esta orden
            $receipt = Receipt::where('order_id', $order->order_id)
                ->latest('receipt_id')
                ->first();

            if (!$receipt || !$receipt->pdf_path || !file_exists(public_path($receipt->pdf_path))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comprobante no encontrado'
                ], 404);
            }

            return response()->download(
                public_path($receipt->pdf_path),
                'Comprobante_' . $order->order_number . '.pdf'
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar comprobante: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reenviar comprobante al email
     */
    public function resendReceipt(Order $order)
    {
        try {
            // Obtener el recibo más reciente
            $receipt = Receipt::where('order_id', $order->order_id)
                ->latest('receipt_id')
                ->first();

            if (!$receipt || !$receipt->pdf_path || !file_exists(public_path($receipt->pdf_path))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comprobante no encontrado'
                ], 404);
            }

            $customer = $order->user->first();
            if (!$customer || !$customer->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene email registrado'
                ], 400);
            }

            $this->sendReceiptEmail($order, $receipt);

            // Actualizar timestamp de envío en tbreceipt
            $receipt->update([
                'sent_to_email' => true,
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comprobante reenviado exitosamente al correo del cliente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reenviar comprobante: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar comprobante por email
     */
    private function sendReceiptEmail(Order $order, Receipt $receipt)
    {
        try {
            $customer = $order->user->first();
            
            if (!$customer || !$customer->email) {
                Log::warning('Intento de envío de comprobante sin cliente o email', [
                    'order_id' => $order->order_id,
                    'receipt_id' => $receipt->receipt_id,
                ]);
                return false;
            }

            // Validar que el PDF existe
            $fullPdfPath = public_path($receipt->pdf_path);
            if (!file_exists($fullPdfPath)) {
                Log::error('PDF de comprobante no encontrado', [
                    'receipt_id' => $receipt->receipt_id,
                    'pdf_path' => $receipt->pdf_path,
                    'full_path' => $fullPdfPath,
                ]);
                return false;
            }

            Log::info('Enviando comprobante por email', [
                'order_id' => $order->order_id,
                'receipt_id' => $receipt->receipt_id,
                'customer_email' => $customer->email,
                'pdf_path' => $fullPdfPath,
            ]);

            Mail::to($customer->email)
                ->send(new ReceiptMail($order, $receipt, $fullPdfPath));

            // Marcar como enviado en tbreceipt
            $receipt->update([
                'sent_to_email' => true,
                'sent_at' => now(),
            ]);

            Log::info('Comprobante enviado exitosamente', [
                'receipt_id' => $receipt->receipt_id,
                'sent_at' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error enviando email de comprobante', [
                'receipt_id' => $receipt->receipt_id ?? null,
                'order_id' => $order->order_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Ver comprobante PDF en navegador
     */
    public function viewReceipt(Order $order)
    {
        try {
            // Obtener el recibo más reciente
            $receipt = Receipt::where('order_id', $order->order_id)
                ->latest('receipt_id')
                ->first();

            if (!$receipt || !$receipt->pdf_path || !file_exists(public_path($receipt->pdf_path))) {
                abort(404, 'Comprobante no encontrado');
            }

            return response()->file(public_path($receipt->pdf_path));

        } catch (\Exception $e) {
            abort(500, 'Error al cargar comprobante: ' . $e->getMessage());
        }
    }

    /**
     * Generar número único de comprobante
     */
    private function generateReceiptNumber()
    {
        // Buscar en tbreceipt el último número de comprobante
        $latestReceipt = Receipt::latest('receipt_id')->first();
        
        // Si hay recibos previos, extraer el número secuencial
        if ($latestReceipt && preg_match('/COMP-(\d+)/', $latestReceipt->receipt_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'COMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT) . '-' . now()->format('Y');
    }

    /**
     * Generar referencia única
     */
    private function generateReference()
    {
        return strtoupper(substr(md5(uniqid() . time()), 0, 12));
    }
}

