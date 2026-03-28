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
}
