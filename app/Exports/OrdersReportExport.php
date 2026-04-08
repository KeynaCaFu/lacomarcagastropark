<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class OrdersReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $local;
    protected $orderStats;
    protected $revenueStats;
    protected $topItems;
    protected $startDate;
    protected $endDate;

    public function __construct($local, $orderStats, $revenueStats, $topItems, $startDate, $endDate)
    {
        $this->local = $local;
        $this->orderStats = $orderStats;
        $this->revenueStats = $revenueStats;
        $this->topItems = $topItems;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE PEDIDOS'],
            [$this->local->name],
            ['Período: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y')],
            [],
            ['RESUMEN POR TIPO DE PEDIDO'],
            ['Tipo', 'Cantidad', 'Porcentaje', 'Ingresos (₡)', 'Promedio (₡)'],
        ];
    }

    public function collection(): Collection
    {
        $webAvg = $this->orderStats['web']['count'] > 0 
            ? $this->revenueStats['web']['revenue'] / $this->orderStats['web']['count'] 
            : 0;
        
        $presentialAvg = $this->orderStats['presential']['count'] > 0 
            ? $this->revenueStats['presential']['revenue'] / $this->orderStats['presential']['count'] 
            : 0;
        
        $totalAvg = $this->orderStats['total'] > 0 
            ? $this->revenueStats['total'] / $this->orderStats['total'] 
            : 0;

        $data = [
            ['En Línea', 
                $this->orderStats['web']['count'], 
                $this->orderStats['web']['percentage'] . '%',
                $this->revenueStats['web']['revenue'],
                round($webAvg, 2)
            ],
            ['Presencial', 
                $this->orderStats['presential']['count'], 
                $this->orderStats['presential']['percentage'] . '%',
                $this->revenueStats['presential']['revenue'],
                round($presentialAvg, 2)
            ],
            ['TOTAL', 
                $this->orderStats['total'], 
                '100%',
                $this->revenueStats['total'],
                round($totalAvg, 2)
            ],
            [],
            ['PRODUCTOS MÁS VENDIDOS'],
            ['Producto', 'Cantidad Vendida', 'Transacciones'],
        ];

        foreach ($this->topItems as $item) {
            $data[] = [
                $item->name,
                $item->total_quantity,
                $item->order_count,
            ];
        }

        return collect($data);
    }

    public function styles(Worksheet $sheet)
    {
        // Aplicar negritas a los encabezados
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A5:A6')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        
        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return [];
    }
}