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
     * Regenerar comprobante PDF (reemplazar si existe, crear si no)
     */
    public function regenerateReceiptAction(Order $order)
    {
        try {
            // Obtener el recibo más reciente
            $receipt = Receipt::where('order_id', $order->order_id)
                ->latest('receipt_id')
                ->first();

            // Variables para guardar datos del recibo anterior
            $paymentMethod = 'Efectivo'; // valor por defecto
            $receiptReference = null;

            // Si existe un comprobante anterior
            if ($receipt) {
                // Obtener datos del recibo anterior
                $paymentMethod = $receipt->payment_method;
                $receiptReference = $receipt->receipt_reference;

                // Eliminar archivo PDF anterior si existe
                if ($receipt->pdf_path && file_exists(public_path($receipt->pdf_path))) {
                    unlink(public_path($receipt->pdf_path));
                }

                // Eliminar registro anterior de Receipt
                $receipt->delete();
            }

            // Cargar relaciones necesarias de la orden
            $order->load(['items.product', 'user', 'local']);

            // Generar nuevo comprobante
            $result = $this->generateReceipt($order, $paymentMethod, $receiptReference);

            if (!$result['success']) {
                return response()->json($result, 500);
            }

            return response()->json([
                'success' => true,
                'message' => $receipt ? 'Comprobante regenerado y guardado correctamente' : 'Comprobante creado y guardado correctamente',
                'receipt_id' => $result['receipt_id'],
                'pdf_path' => $result['pdf_path']
            ]);

        } catch (\Exception $e) {
            Log::error('Error al regenerar comprobante', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al regenerar comprobante: ' . $e->getMessage()
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

    /**
     * Ver historial de órdenes entregadas y canceladas
     */
    public function viewOrderHistory()
    {
        try {
            // Obtener órdenes de hoy y hace 5 días
            $fiveDaysAgo = now()->subDays(5)->startOfDay();
            $today = now()->endOfDay();

            // Obtener órdenes entregadas (del día actual y últimos 5 días)
            $deliveredOrders = Order::where('status', 'Delivered')
                ->whereBetween('updated_at', [$fiveDaysAgo, $today])
                ->with(['items.product', 'user', 'local', 'receipts'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->groupBy(function($order) {
                    $date = $order->updated_at->toDateString();
                    $today = now()->toDateString();
                    return $date === $today ? 'Hoy' : $order->updated_at->format('d/m/Y');
                });

            // Obtener órdenes canceladas (del día actual y últimos 5 días)
            $cancelledOrders = Order::where('status', 'Cancelled')
                ->whereBetween('updated_at', [$fiveDaysAgo, $today])
                ->with(['items.product', 'user', 'local', 'receipts'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->groupBy(function($order) {
                    $date = $order->updated_at->toDateString();
                    $today = now()->toDateString();
                    return $date === $today ? 'Hoy' : $order->updated_at->format('d/m/Y');
                });

            $statuses = [
                'Pending' => 'Pendiente',
                'In Progress' => 'En Preparación',
                'Ready' => 'Listo',
                'Delivered' => 'Entregado',
                'Cancelled' => 'Cancelada',
            ];

            return view('orders.history', compact('deliveredOrders', 'cancelledOrders', 'statuses'));

        } catch (\Exception $e) {
            Log::error('Error al cargar historial de órdenes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Error al cargar historial de órdenes');
        }
    }

    /**
     * Buscar órdenes con filtros (endpoint AJAX)
     */
    public function searchOrderHistory(Request $request)
    {
        try {
            $orderNumber = $request->input('orderNumber');
            $customerName = $request->input('customerName');
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');

            // Si no hay filtros, devolver error
            if (!$orderNumber && !$customerName && !$dateFrom && !$dateTo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe proporcionar al menos un filtro'
                ], 400);
            }

            $query = Order::whereIn('status', ['Delivered', 'Cancelled'])
                ->with(['items.product', 'user', 'local', 'receipts']);

            // Filtro por número de orden
            if ($orderNumber) {
                $query->where('order_number', 'like', "%$orderNumber%");
            }

            // Filtro por nombre de cliente
            if ($customerName) {
                $query->whereHas('user', function($q) use ($customerName) {
                    $q->where('full_name', 'like', "%$customerName%");
                });
            }

            // Filtro por rango de fechas
            if ($dateFrom) {
                $query->whereDate('updated_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('updated_at', '<=', $dateTo);
            }

            $orders = $query->orderBy('updated_at', 'desc')->get();

            // Agrupar por estado y fecha
            $groupedOrders = $orders->groupBy('status')->map(function($statusOrders, $status) {
                return $statusOrders->groupBy(function($order) {
                    $date = $order->updated_at->toDateString();
                    $today = now()->toDateString();
                    return $date === $today ? 'Hoy' : $order->updated_at->format('d/m/Y');
                });
            });

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'grouped' => $groupedOrders,
                'count' => $orders->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al buscar órdenes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar órdenes: ' . $e->getMessage()
            ], 500);
        }
    }
}

