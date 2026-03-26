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

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
        ]);

        try {
            $this->orderData->changeStatus($orderId, $validated['status']);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $validated['status']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
