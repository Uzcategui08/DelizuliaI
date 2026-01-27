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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumenTrabajosSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
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
            ['RESUMEN DE TRABAJOS'],
            [''],
            ['Trabajo', 'Cantidad']
        ];
    }

    public function collection()
    {
        $data = [];
        
        $trabajos = $this->data['resumenTrabajos'] ?? collect();

        foreach ($trabajos as $trabajo) {
            $data[] = [
                'trabajo' => $trabajo['nombre'],
                'cantidad' => $trabajo['cantidad']
            ];
        }

        $data[] = [
            'trabajo' => 'TOTALES',
            'cantidad' => $trabajos->sum('cantidad')
        ];

        return collect($data);
    }

    public function title(): string
    {
        return 'Resumen de Trabajos';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->getDelegate()->getColumnDimension('A')->setAutoSize(false);
                $sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);

                $sheet->getDelegate()->getColumnDimension('A')->setWidth(50); 
                $sheet->getDelegate()->getColumnDimension('B')->setWidth(20);

                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                $sheet->mergeCells('A1:B1');
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

                $sheet->mergeCells('A2:B2');
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

                $sheet->mergeCells('A4:B4');
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

                $sheet->getStyle('A6:B6')->applyFromArray([
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

                $sheet->getStyle('A7:B' . $highestRow)->applyFromArray([
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

                foreach (range(7, $highestRow) as $row) {
                    $color = $row % 2 == 0 ? 'FFFFFF' : 'F9F9F9';
                    $sheet->getStyle("A{$row}:B{$row}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color);
                }

                $sheet->freezePane('A7');

                $sheet->setAutoFilter('A6:B' . $highestRow);
            }
        ];
    }
}