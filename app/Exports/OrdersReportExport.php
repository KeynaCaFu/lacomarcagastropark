<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromApplication;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PHPExcel\Style\Font;
use PhpOffice\PHPExcel\Style\Alignment;
use PhpOffice\PHPExcel\Style\Fill;

class OrdersReportExport implements FromApplication, WithHeadings, WithStyles, ShouldAutoSize
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

    public function collection()
    {
        $rows = [];

        // Encabezado
        $rows[] = ['REPORTE DE PEDIDOS - ' . strtoupper($this->local->name)];
        $rows[] = [];
        $rows[] = ['Período: ' . $this->startDate->format('d/m/Y') . ' a ' . $this->endDate->format('d/m/Y')];
        $rows[] = ['Generado: ' . \Carbon\Carbon::now()->format('d/m/Y H:i')];
        $rows[] = [];

        // Resumen de pedidos
        $rows[] = ['RESUMEN DE PEDIDOS'];
        $rows[] = [];
        $rows[] = [
            'Total de Pedidos',
            'En Línea',
            'Presencial',
            '% En Línea',
            '% Presencial'
        ];
        $rows[] = [
            $this->orderStats['total'],
            $this->orderStats['web']['count'],
            $this->orderStats['presential']['count'],
            $this->orderStats['web']['percentage'] . '%',
            $this->orderStats['presential']['percentage'] . '%',
        ];
        $rows[] = [];

        // Resumen de ingresos
        $rows[] = ['RESUMEN DE INGRESOS'];
        $rows[] = [];
        $rows[] = [
            'Total Ingresos',
            'En Línea',
            'Presencial',
            '% En Línea',
            '% Presencial',
            'Ticket Promedio'
        ];
        
        $avgTicket = $this->orderStats['total'] > 0 
            ? round($this->revenueStats['total'] / $this->orderStats['total'], 2)
            : 0;

        $rows[] = [
            '₡' . number_format($this->revenueStats['total'], 2),
            '₡' . number_format($this->revenueStats['web']['revenue'], 2),
            '₡' . number_format($this->revenueStats['presential']['revenue'], 2),
            $this->revenueStats['web']['percentage'] . '%',
            $this->revenueStats['presential']['percentage'] . '%',
            '₡' . number_format($avgTicket, 2),
        ];
        $rows[] = [];

        // Productos más vendidos
        if ($this->topItems->count() > 0) {
            $rows[] = ['PRODUCTOS MÁS VENDIDOS'];
            $rows[] = [];
            $rows[] = [
                'Producto',
                'Cantidad Vendida',
                'Número de Órdenes'
            ];

            foreach ($this->topItems as $item) {
                $rows[] = [
                    $item->name,
                    $item->total_quantity,
                    $item->order_count
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [];
    }

    public function styles($sheet)
    {
        // Estilos para el título
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('485a1a');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('ffffff');

        return [
            // Estilos para encabezados de secciones
            9 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'ffffff']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '485a1a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            14 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'ffffff']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '485a1a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            19 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'ffffff']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '485a1a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }
}
