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

class CostosSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
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
            ['DETALLE DE COSTOS'],
            [''],
            ['Técnico', 'Descripción', 'Método Pago', 'Total']
        ];
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->data['reporteCostosGastos'] as $item) {
            $costosCount = count($item['costos'] ?? []);
            
            for ($i = 0; $i < $costosCount; $i++) {
                $tecnico = $i === 0 ? $item['tecnico'] : '';
                
                $costoDescripcion = isset($item['costos'][$i]) ? $item['costos'][$i]['descripcion'] : '';
                $costoMetodoPago = isset($item['costos'][$i]) ? $item['costos'][$i]['metodo_pago'] : '';
                $costoTotal = isset($item['costos'][$i]['total']) ? $item['costos'][$i]['total'] : 0;
                $costoMetodoPago = isset($item['costos'][$i]['metodo_pago']) ? $this->getMetodoPago($item['costos'][$i]['metodo_pago']) : 'No especificado';
                
                $data[] = [
                    'tecnico' => $tecnico,
                    'descripcion' => $costoDescripcion,
                    'metodo_pago' => $costoMetodoPago,
                    'total' => $costoTotal
                ];
            }
        }

        $totalCostos = 0;
        foreach ($this->data['reporteCostosGastos'] as $item) {
            foreach ($item['costos'] as $costo) {
                $totalCostos += $costo['total'] ?? 0;
            }
        }

        $data[] = [
            'tecnico' => 'TOTALES',
            'descripcion' => '',
            'metodo_pago' => '',
            'total' => $totalCostos
        ];

        return collect($data);
    }

    public function title(): string
    {
        return 'Costos';
    }

    private function getMetodoPago($metodo)
    {
        if (is_numeric($metodo)) {
            $metodo = \App\Models\TiposDePago::find($metodo);
            return $metodo ? $metodo->name : $metodo;
        }

        if (is_string($metodo)) {
            return $metodo;
        }

        return 'Sin especificar';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->setAutoFilter("A6:D{$highestRow}");

                $sheet->freezePane('A7');

                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->mergeCells('A1:D1');
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

                $sheet->mergeCells('A2:D2');
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

                $sheet->mergeCells('A4:D4');
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

                $sheet->getStyle('A6:D6')->applyFromArray([
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

                $sheet->getStyle('A7:D' . $highestRow)->applyFromArray([
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

                $sheet->getStyle('A7:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B7:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('D7:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('D7:D' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode('[$$-409]#,##0.00');

                foreach (range(7, $highestRow) as $row) {
                    $color = $row % 2 == 0 ? 'FFFFFF' : 'F9F9F9';
                    $sheet->getStyle("A{$row}:D{$row}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color);
                }

                $sheet->getDefaultRowDimension()->setRowHeight(20);

                $sheet->freezePane('A7');
            }
        ];
    }
}