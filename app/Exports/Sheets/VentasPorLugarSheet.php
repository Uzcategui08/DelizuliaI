<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class VentasPorLugarSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE CIERRE SEMANAL'],
            ['Del ' . $this->data['startDate'] . ' al ' . $this->data['endDate']],
            [''],
            ['VENTAS POR LUGAR DE VENTA'],
            [''],
            ['Lugar de Venta', 'Cantidad de Ventas', 'Monto Total']
        ];
    }

    public function collection()
    {
        $data = [];
        
        $ventasPorLugar = $this->data['ventasPorLugarVenta'] ?? collect();

        foreach ($ventasPorLugar as $lugar) {
            $data[] = [
                'lugar_venta' => $lugar['nombre'] ?? 'Sin especificar',
                'cantidad' => $lugar['cantidad'] ?? 0,
                'monto' => $lugar['monto'] ?? 0
            ];
        }

        $data[] = [
            'lugar_venta' => 'TOTALES',
            'cantidad' => $ventasPorLugar->sum('cantidad') ?? 0,
            'monto' => $ventasPorLugar->sum('monto') ?? 0
        ];

        return collect($data);
    }

    public function title(): string
    {
        return 'Ventas por Lugar de Venta';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '2A5885']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                $sheet->mergeCells('A2:C2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 12,
                        'color' => ['rgb' => '555555']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                $sheet->mergeCells('A4:C4');
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '2A5885']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6EFF7']
                    ]
                ]);

                $sheet->getStyle('A6:C6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2A5885']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD']
                        ]
                    ]
                ]);

                $sheet->getStyle('A7:C' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD']
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP
                    ]
                ]);

                $sheet->getStyle('C7:C' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode('[$$-409]#,##0.00');

                foreach (range(7, $highestRow) as $row) {
                    $color = $row % 2 == 0 ? 'FFFFFF' : 'F9F9F9';
                    $sheet->getStyle("A{$row}:C{$row}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color);
                }

                $sheet->freezePane('A7');

                $sheet->setAutoFilter('A6:C' . $highestRow);
            }
        ];
    }
}