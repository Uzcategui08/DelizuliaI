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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\NominaService;

class ResumenFinancieroSheet implements FromArray, WithTitle, WithStyles, WithColumnFormatting, WithEvents
{
    protected $data;
    protected $nominaService;

    public function __construct($data)
    {
        $this->data = $data;
        $this->nominaService = new NominaService();
    }

    public function array(): array
    {
        $rows = [];
        
        $rows[] = ['Resumen Financiero Semanal'];
        $rows[] = ['Del ' . $this->data['startDate'] . ' al ' . $this->data['endDate']];

        $rows[] = ['Ventas Totales', 'Total Costos', 'Costos de Llaves', 'Total Gastos', 'Ganancia Operativa antes del retiro del Dueño'];

        $rows[] = [
            $this->data['totales']['totalVentas'] ?? 0,
            $this->data['totales']['totalCostos'] ?? 0,
            $this->data['totalCostosLlaves'] ?? 0, 
            $this->data['totales']['totalGastos'] ?? 0,
            $this->data['ganancia'] ?? 0
        ];
        
        $rows[] = ['', '', '', '', 'Retiro Dueño'];
        $rows[] = ['', '', '', '', $this->nominaService->calcularRetiroDueño(
            $this->data['startDate'],
            $this->data['endDate']
        )];
        $rows[] = ['', '', '', '', 'Ganancia Total'];
        $rows[] = ['', '', '', '', $this->data['gananciaFinal'] ?? 0];
        
        return $rows;
    }

    public function title(): string
    {
        return 'Resumen Financiero';
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(70);

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

        $sheet->getStyle('A3:E3')->applyFromArray([
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

        $sheet->getStyle('A4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '000000']
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

        $sheet->getRowDimension(4)->setRowHeight(30);

        $sheet->getStyle('E5:E8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('E5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('E7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('E8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '2A5885']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6EFF7']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('A4:A' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');        
        $sheet->getStyle('B4:B' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('C4:C' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');

        $sheet->getStyle('B4')->getNumberFormat()->setFormatCode('$#,##0.00');

        return []; 
    }

    public function columnFormats(): array
    {
        return [
            'B' => '$#,##0.00',  
            'C' => '$#,##0.00',
            'D' => '$#,##0.00',
            'E' => '$#,##0.00'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A4');

                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}