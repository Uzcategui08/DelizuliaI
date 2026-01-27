<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ResumenVentasSheet implements FromArray, WithTitle, WithStyles, WithColumnFormatting, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        
        $rows[] = ['Resumen de Ventas'];
        $rows[] = ['Del ' . $this->data['startDate'] . ' al ' . $this->data['endDate']];
        
        $rows[] = ['Técnico', 'Ventas al Contado', 'Ventas a Crédito', 'Ingresos Recibidos', 'Total General'];
        
        $totalContado = 0;
        $totalCredito = 0;
        $totalRecibidos = 0;
        $totalGeneral = 0;
        
        foreach ($this->data['reporteVentas'] as $index => $venta) {
            $ingresos = $this->data['ingresosRecibidos'][$index]['total'] ?? 0;
            $totalGeneralVenta = $venta['total_ventas'] + $ingresos;
            
            $rows[] = [
                $venta['tecnico'],
                $venta['ventas_contado'],
                $venta['ventas_credito'],
                $ingresos,
                $totalGeneralVenta
            ];
            
            $totalContado += $venta['ventas_contado'];
            $totalCredito += $venta['ventas_credito'];
            $totalRecibidos += $ingresos;
            $totalGeneral += $totalGeneralVenta;
        }

        $rows[] = [
            'TOTAL',
            $totalContado,
            $totalCredito,
            $totalRecibidos,
            $totalGeneral
        ];
        
        return $rows;
    }

    public function title(): string
    {
        return 'Resumen Ventas';
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
 
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);

        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '2A5885']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 12,
                'color' => ['rgb' => '555555']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $headerStyle = [
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
        ];
        $sheet->getStyle('A3:E3')->applyFromArray($headerStyle);

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP
            ]
        ];
        $sheet->getStyle('A4:E' . $highestRow)->applyFromArray($dataStyle);

        $totalStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '2A5885']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6EFF7']
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
        ];
        $sheet->getStyle('A' . $highestRow . ':E' . $highestRow)->applyFromArray($totalStyle);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->setAutoFilter('A3:E' . $highestRow);

                $sheet->freezePane('A4');

                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->getStyle('B4:E' . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'font' => [
                        'color' => ['rgb' => '333333'] 
                    ]
                ]);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }
}