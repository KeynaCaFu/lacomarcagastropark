<?php

namespace App\Data;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderData
{
    protected $table = 'tborder';

    /**
     * Obtener todas las órdenes con filtros opcionales
     */
    public function all(array $filters = [])
    {
        $query = Order::with(['items.product', 'local']);

        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filtro por local
        if (!empty($filters['local_id'])) {
            $query->byLocal($filters['local_id']);
        }

        // Filtro por fecha
        if (!empty($filters['date'])) {
            $query->byDate($filters['date']);
        }

        // Ordenar por más recientes primero
        $query->orderByDesc('created_at');

        return $query->paginate(15);
    }

    /**
     * Obtener órdenes por estado
     */
    public function getByStatus($status, $localId = null)
    {
        $query = Order::byStatus($status)->with(['items.product', 'local']);

        if ($localId) {
            $query->byLocal($localId);
        }

        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Obtener orden por ID
     */
    public function getById($orderId)
    {
        return Order::with(['items.product', 'local'])
            ->find($orderId);
    }

    /**
     * Crear nueva orden
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            // Generar número de orden único
            $data['order_number'] = $this->generateOrderNumber();
            $data['status'] = Order::STATUS_PENDING;

            // Crear orden
            $order = Order::create($data);

            // Agregar ítems
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'customization' => $item['customization'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return $order->load(['items.product', 'local']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar orden
     */
    public function update($orderId, array $data)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);
            $order->update($data);

            // Si se proporcionan items, actualizar
            if (isset($data['items'])) {
                // Eliminar items antiguos
                OrderItem::where('order_id', $orderId)->delete();

                // Agregar nuevos items
                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'customization' => $item['customization'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return $order->load(['items.product', 'local']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cambiar estado de orden
     */
    public function changeStatus($orderId, $newStatus)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $newStatus]);
        return $order;
    }

    /**
     * Eliminar orden
     */
    public function delete($orderId)
    {
        $order = Order::findOrFail($orderId);
        // Eliminar items primero
        OrderItem::where('order_id', $orderId)->delete();
        return $order->delete();
    }

    /**
     * Obtener conteos por estado para un local
     */
    public function getCountsByStatus($localId = null)
    {
        $statuses = Order::getStatuses();
        $counts = [];

        foreach (array_keys($statuses) as $status) {
            $query = Order::query();
            
            if ($localId) {
                $query->byLocal($localId);
            }
            
            $counts[$status] = $query->byStatus($status)->count();
        }

        // Total count
        $totalQuery = Order::query();
        if ($localId) {
            $totalQuery->byLocal($localId);
        }
        $counts['total'] = $totalQuery->count();

        return $counts;
    }

    /**
     * Obtener órdenes del día
     */
    public function getTodayOrders($localId = null)
    {
        $query = Order::byDate(now()->toDateString())->with(['items.product', 'local']);

        if ($localId) {
            $query->byLocal($localId);
        }

        return $query->orderByDesc('time')->get();
    }

    /**
     * Obtener ingresos por período
     */
    public function getRevenueByPeriod($localId = null, $startDate = null, $endDate = null)
    {
        $query = Order::whereIn('status', [Order::STATUS_DELIVERED])
            ->selectRaw('DATE(date) as date, SUM(total_amount) as revenue, COUNT(*) as count');

        if ($localId) {
            $query->byLocal($localId);
        }

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return $query->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Obtener productos más vendidos
     */
    public function getTopProducts($localId = null, $limit = 10)
    {
        $query = OrderItem::selectRaw('product_id, COUNT(*) as total_sold, SUM(quantity) as total_quantity')
            ->whereHas('order', function ($q) use ($localId) {
                $q->whereIn('status', [Order::STATUS_DELIVERED]);
                if ($localId) {
                    $q->byLocal($localId);
                }
            });

        return $query->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * Generar número de orden único
     */
    private function generateOrderNumber()
    {
        $lastOrder = Order::orderByDesc('order_id')->first();
        $nextNumber = ($lastOrder ? intval(substr($lastOrder->order_number, -4)) : 0) + 1;
        return 'ORD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener información general de órdenes
     */
    public function getDashboardStats($localId = null, $days = 30)
    {
        $query = Order::recent($days);

        if ($localId) {
            $query->byLocal($localId);
        }

        $totalOrders = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'pending_orders' => Order::byStatus(Order::STATUS_PENDING)->when($localId, function ($q) use ($localId) {
                return $q->byLocal($localId);
            })->count(),
            'in_preparation' => Order::byStatus(Order::STATUS_PREPARATION)->when($localId, function ($q) use ($localId) {
                return $q->byLocal($localId);
            })->count(),
        ];
    }
}
