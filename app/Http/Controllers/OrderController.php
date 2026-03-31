<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\OrderData;
use App\Models\Order;

class OrderController extends Controller
{
    protected $orderData;

    public function __construct(OrderData $orderData)
    {
        $this->orderData = $orderData;
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

        // Obtener órdenes pendientes
        $pendingOrders = Order::where('status', 'Pending')
            ->when($localId, function ($query) use ($localId) {
                return $query->where('local_id', $localId);
            })
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
                'created_at' => $order->created_at->format('H:i')
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
            ]);

            // Agregar items a la orden
            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'customization' => $item['customization'] ?? null,
                ]);
            }

            // Asociar cliente si se proporciona
            if ($validated['user_id'] ?? null) {
                $order->user()->attach($validated['user_id']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'order' => [
                    'order_id' => $order->order_id,
                    'order_number' => $order->order_number,
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
}
