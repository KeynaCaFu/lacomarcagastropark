<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\OrderData;
use App\Models\Order;
use App\Models\Receipt;
use App\Http\Controllers\ReceiptController;
use App\Services\OrderTokenService;

class OrderController extends Controller
{
    protected $orderData;
    protected OrderTokenService $tokenService;

    public function __construct(OrderData $orderData, OrderTokenService $tokenService)
    {
        $this->orderData = $orderData;
        $this->tokenService = $tokenService;
    }

    /**
     * Mostrar lista de órdenes con filtros
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = [
            'search' => $request->input('buscar'),
            'status' => $request->input('estado'),
            'date' => $request->input('fecha'),
        ];

        // Si es gerente de local, filtrar por su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if (!$local) {
                return view('orders.index', [
                    'orders' => collect(),
                    'statuses' => Order::getStatuses(),
                    'counts' => [],
                ]);
            }
            $filters['local_id'] = $local->local_id;
        }

        $orders = $this->orderData->all($filters);
        $statuses = Order::getStatuses();
        $counts = $this->orderData->getCountsByStatus($filters['local_id'] ?? null);

        return view('orders.index', compact('orders', 'statuses', 'counts'));
    }

    /**
     * Mostrar detalles de la orden (AJAX)
     */
    public function show($orderId)
    {
        $order = $this->orderData->getById($orderId);

        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        // Cargar usuario que realizó la orden
        $order->load('user');

        // Verificar permisos
        $user = auth()->user();
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if (!$local || $order->local_id !== $local->local_id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        // Retornar vista parcial para AJAX
        return view('orders._show_details', [
            'order' => $order,
            'statuses' => Order::getStatuses(),
        ]);
    }

    /**
     * Cambiar estado de la orden
     */
    public function changeStatus(Request $request, $orderId)
    {
        $order = $this->orderData->getById($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'error' => 'Orden no encontrada'], 404);
        }

        // Verificar permisos
        $user = auth()->user();
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if (!$local || $order->local_id !== $local->local_id) {
                return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
            }
        }

        try {
            $statuses = array_keys(Order::getStatuses());
            $newStatus = $request->input('status');
            
            if (!in_array($newStatus, $statuses)) {
                return response()->json([
                    'success' => false,
                    'error' => "Estado inválido: {$newStatus}"
                ], 422);
            }

            // Validar el flujo de estados
            if (!$this->isValidStatusTransition($order->status, $newStatus)) {
                return response()->json([
                    'success' => false,
                    'error' => "No se puede cambiar de {$order->status} a {$newStatus}. Flujo inválido."
                ], 422);
            }

            // Validaciones específicas para cancelación
            if ($newStatus === Order::STATUS_CANCELLED) {
                // No se puede cancelar órdenes entregadas
                if ($order->status === Order::STATUS_DELIVERED) {
                    return response()->json([
                        'success' => false,
                        'error' => 'No se pueden cancelar órdenes que ya han sido entregadas.'
                    ], 422);
                }

                // El motivo de cancelación es obligatorio
                $cancellationReason = $request->input('cancellation_reason', '');
                if (empty(trim($cancellationReason))) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Debe proporcionar un motivo para cancelar la orden.'
                    ], 422);
                }

                // Guardar el motivo de cancelación
                $order->update(['cancellation_reason' => $cancellationReason]);
            }

            // Validaciones para cambio a DELIVERED - requiere datos de pago
            if ($newStatus === Order::STATUS_DELIVERED && $order->status === Order::STATUS_READY) {
                $paymentMethod = $request->input('payment_method', '');
                $receiptReference = $request->input('receipt_reference', '');

                if (empty(trim($paymentMethod))) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Debe seleccionar un método de pago.'
                    ], 422);
                }

                if (empty(trim($receiptReference))) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Debe proporcionar un número de comprobante/factura.'
                    ], 422);
                }

                // Cambiar estado primero
                $this->orderData->changeStatus($orderId, $newStatus);

                // Verificar si debe saltar la generación de comprobante (para Gerentes)
                $skipReceiptGeneration = $request->input('skip_receipt_generation', false);
                
                // Generar comprobante
                if ($skipReceiptGeneration) {
                    // Para Gerentes: guardar los datos en tbreceipt pero sin PDF
                    
                    // Generar número de comprobante
                    $receiptNumber = \Carbon\Carbon::now()->format('Ymd') . '-' . str_pad(
                        Receipt::count() + 1,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );
                    
                    // Crear registro sin PDF
                    Receipt::create([
                        'order_id' => $orderId,
                        'receipt_number' => $receiptNumber,
                        'payment_method' => $paymentMethod,
                        'receipt_reference' => $receiptReference,
                        'pdf_path' => null,
                        'sent_to_email' => false,
                    ]);
                } else {
                    // Para Clientes: generar comprobante completo con PDF
                    $receiptController = app(ReceiptController::class);
                    $receiptResult = $receiptController->generateReceipt(
                        $order,
                        $paymentMethod,
                        $receiptReference
                    );

                    if (!$receiptResult['success']) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Estado actualizado pero error al generar comprobante: ' . $receiptResult['message']
                        ], 500);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $skipReceiptGeneration 
                        ? 'Estado actualizado. Para generar el comprobante, dirígete al Historial de Órdenes.'
                        : 'Estado actualizado y comprobante generado',
                    'status' => $newStatus
                ]);
            }

            $this->orderData->changeStatus($orderId, $newStatus);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar si la transición de estado es permitida
     */
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        // Flujo permitido de estados
        $allowedTransitions = [
            Order::STATUS_PENDING => [Order::STATUS_PREPARATION, Order::STATUS_CANCELLED],
            Order::STATUS_PREPARATION => [Order::STATUS_READY, Order::STATUS_CANCELLED],
            Order::STATUS_READY => [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED],
            Order::STATUS_DELIVERED => [],
            Order::STATUS_CANCELLED => []
        ];

        // Si el estado actual no existe en la definición, no es válido
        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        // Verificar si el nuevo estado está en los permitidos
        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    /**
     * Obtener contador de órdenes pendientes (JSON para la campana)
     */
    public function getPendingCount()
    {
        $user = auth()->user();
        $localId = null;

        // Si es gerente de local, obtener su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if (!$local) {
                return response()->json(['count' => 0, 'orders' => []]);
            }
            $localId = $local->local_id;
        }

        // Obtener órdenes pendientes con sus items
        $pendingOrders = Order::where('status', 'Pending')
            ->when($localId, function ($query) use ($localId) {
                return $query->where('local_id', $localId);
            })
            ->with('items.product:product_id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['order_id', 'order_number', 'status', 'created_at']);

        $count = Order::where('status', 'Pending')
            ->when($localId, function ($query) use ($localId) {
                return $query->where('local_id', $localId);
            })
            ->count();

        return response()->json([
            'count' => $count,
            'orders' => $pendingOrders->map(fn($order) => [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'created_at' => $order->created_at->format('H:i'),
                'items' => $order->items->map(fn($item) => [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity
                ])->toArray()
            ])
        ]);
    }

    /**
     * Obtener productos disponibles del local para crear orden (AJAX)
     */
    public function getLocalProducts()
    {
        $user = auth()->user();

        if (!$user->isAdminLocal()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $local = $user->locals()->first();
        if (!$local) {
            return response()->json(['products' => []]);
        }

        // Obtener productos activos del local con sus precios
        $products = $local->products()
            ->where('tbproduct.status', 'Available')
            ->get()
            ->map(fn($product) => [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'photo' => $product->photo_url,
                'price' => $product->pivot->price ?? 0,
                'category' => $product->category ?? 'Sin categoría',
            ]);

        return response()->json(['products' => $products]);
    }

    /**
     * Crear una nueva orden (presencial)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isAdminLocal()) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }

        $local = $user->locals()->first();
        if (!$local) {
            return response()->json(['success' => false, 'error' => 'Local no asignado'], 400);
        }

        try {
            $validated = $request->validate([
                'user_id' => 'nullable|exists:tbuser,user_id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:tbproduct,product_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.customization' => 'nullable|string|max:500',
                'preparation_time' => 'required|integer|min:1',
                'additional_notes' => 'nullable|string|max:500',
            ]);

            // ═════════════════════════════════════════════════════════════════
            // CA2: Generar token único de verificación (LCGP-XXXX)
            // ═════════════════════════════════════════════════════════════════
            $verificationToken = $this->tokenService->generateUniqueToken();
            
            // ═════════════════════════════════════════════════════════════════
            // CA5: Registrar timestamp exacto de confirmación
            // ═════════════════════════════════════════════════════════════════
            $confirmedAt = now();

            // Generar número único de orden ORD-XXXX
            $orderNumber = $this->generateOrderNumber();

            // Calcular total
            $totalAmount = 0;
            $quantity = 0;

            foreach ($validated['items'] as $item) {
                $localProduct = $local->products()
                    ->where('tbproduct.product_id', $item['product_id'])
                    ->first();

                if (!$localProduct) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Producto no disponible en este local'
                    ], 422);
                }

                $price = $localProduct->pivot->price ?? 0;
                $totalAmount += $price * $item['quantity'];
                $quantity += $item['quantity'];
            }

            // Crear la orden
            $order = Order::create([
                'order_number' => $orderNumber,
                'local_id' => $local->local_id,
                'status' => Order::STATUS_PENDING,
                'origin' => 'presencial',
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'preparation_time' => $validated['preparation_time'],
                'additional_notes' => $validated['additional_notes'] ?? null,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'verification_token' => $verificationToken,  // CA2: Token único
                'confirmed_at' => $confirmedAt,             // CA5: Timestamp
            ]);

            // Agregar items a la orden
            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'customization' => $item['customization'] ?? null,
                ]);
            }

            // Guardar relación en tblocal_orden
            $order->locals()->attach($local->local_id);

            // Asociar cliente o gerente si corresponde
            $userToAttach = $validated['user_id'] ?? $user->user_id;
            $order->user()->attach($userToAttach);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'order' => [
                    'order_id' => $order->order_id,
                    'order_number' => $order->order_number,
                    'verification_token' => $order->verification_token,  // CA2: Token único
                    'confirmed_at' => $order->confirmed_at,             // CA5: Timestamp
                    'qr_url' => route('api.orders.validate', ['key' => $order->verification_token], false),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar número único de orden
     */
    private function generateOrderNumber()
    {
        // ORD-XXXX con 4 números aleatorios
        return 'ORD-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buscar clientes por nombre o email
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->input('query', '');

        $query = \App\Models\User::join('tbrole', 'tbuser.role_id', '=', 'tbrole.role_id')
            ->where('tbuser.status', 'active')
            ->where('tbrole.role_type', 'Cliente');

        if (!empty($search) && strlen($search) >= 2) {
            $query->where(function ($q) use ($search) {
                $q->where('tbuser.full_name', 'like', "%{$search}%")
                  ->orWhere('tbuser.email', 'like', "%{$search}%")
                  ->orWhere('tbuser.phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->select(['tbuser.user_id', 'tbuser.full_name', 'tbuser.email', 'tbuser.phone'])
            ->orderBy('tbuser.full_name', 'asc')
            ->limit(20)
            ->get();

        return response()->json(['customers' => $customers]);
    }
}
