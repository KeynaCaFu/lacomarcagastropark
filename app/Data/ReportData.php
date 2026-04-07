<?php

namespace App\Data;

use App\Models\Order;
use Carbon\Carbon;

class ReportData
{
    /**
     * Obtener estadísticas de pedidos por origen
     * 
     * @param int $localId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getOrdersByOrigin($localId, $startDate, $endDate)
    {
        // Contar pedidos por origen
        $webOrders = Order::where('local_id', $localId)
            ->where('origin', Order::ORIGIN_WEB)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $presentialOrders = Order::where('local_id', $localId)
            ->where('origin', Order::ORIGIN_PRESENCIAL)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $total = $webOrders + $presentialOrders;

        // Calcular porcentajes (evitar división por cero)
        $webPercentage = $total > 0 ? round(($webOrders / $total) * 100, 2) : 0;
        $presentialPercentage = $total > 0 ? round(($presentialOrders / $total) * 100, 2) : 0;

        // Ajustar decimales para que sumen exactamente 100%
        if ($total > 0) {
            $difference = 100 - ($webPercentage + $presentialPercentage);
            if ($difference != 0) {
                $webPercentage += $difference;
            }
        }

        return [
            'web' => [
                'count' => $webOrders,
                'percentage' => $webPercentage,
                'label' => 'En Línea'
            ],
            'presential' => [
                'count' => $presentialOrders,
                'percentage' => $presentialPercentage,
                'label' => 'Presencial'
            ],
            'total' => $total,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'startFormatted' => $startDate->format('d/m/Y'),
                'endFormatted' => $endDate->format('d/m/Y'),
            ]
        ];
    }

    /**
     * Obtener detalles de ingresos por origen
     * 
     * @param int $localId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getRevenueByOrigin($localId, $startDate, $endDate)
    {
        $webRevenue = Order::where('local_id', $localId)
            ->where('origin', Order::ORIGIN_WEB)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_amount');

        $presentialRevenue = Order::where('local_id', $localId)
            ->where('origin', Order::ORIGIN_PRESENCIAL)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalRevenue = $webRevenue + $presentialRevenue;

        return [
            'web' => [
                'revenue' => round($webRevenue, 2),
                'percentage' => $totalRevenue > 0 ? round(($webRevenue / $totalRevenue) * 100, 2) : 0,
            ],
            'presential' => [
                'revenue' => round($presentialRevenue, 2),
                'percentage' => $totalRevenue > 0 ? round(($presentialRevenue / $totalRevenue) * 100, 2) : 0,
            ],
            'total' => round($totalRevenue, 2)
        ];
    }

    /**
     * Obtener tendencia diaria de pedidos
     * 
     * @param int $localId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getDailyTrend($localId, $startDate, $endDate)
    {
        $orders = Order::where('local_id', $localId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, origin, COUNT(*) as count')
            ->groupBy('date', 'origin')
            ->orderBy('date')
            ->get();

        $trend = [];
        $currentDate = $startDate->clone();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $trend[$dateStr] = [
                'date' => $currentDate->format('d/m'),
                'web' => 0,
                'presential' => 0,
            ];
            $currentDate->addDay();
        }

        foreach ($orders as $order) {
            $dateStr = $order->date->format('Y-m-d');
            if (isset($trend[$dateStr])) {
                if ($order->origin === Order::ORIGIN_WEB) {
                    $trend[$dateStr]['web'] = $order->count;
                } else {
                    $trend[$dateStr]['presential'] = $order->count;
                }
            }
        }

        return array_values($trend);
    }

    /**
     * Obtener fechas predefinidas según período
     * 
     * @param string $period
     * @return array
     */
    public function getPeriodDates($period = 'today')
    {
        $today = Carbon::now()->startOfDay();
        
        return match ($period) {
            'today' => [
                'start' => $today,
                'end' => $today->clone()->endOfDay(),
                'label' => 'Hoy'
            ],
            'week' => [
                'start' => $today->clone()->startOfWeek(),
                'end' => $today->clone()->endOfWeek(),
                'label' => 'Esta Semana'
            ],
            'month' => [
                'start' => $today->clone()->startOfMonth(),
                'end' => $today->clone()->endOfMonth(),
                'label' => 'Este Mes'
            ],
            'year' => [
                'start' => $today->clone()->startOfYear(),
                'end' => $today->clone()->endOfYear(),
                'label' => 'Este Año'
            ],
            default => [
                'start' => $today,
                'end' => $today,
                'label' => 'Personalizdo'
            ]
        };
    }

    /**
     * Validar que las fechas sean válidas
     */
    public function validateDateRange($startDate, $endDate)
    {
        try {
            $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

            if ($start > $end) {
                return [
                    'valid' => false,
                    'message' => 'La fecha de inicio debe ser menor que la fecha de fin'
                ];
            }

            return [
                'valid' => true,
                'start' => $start,
                'end' => $end
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Formato de fecha inválido'
            ];
        }
    }
}
