<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use App\Models\TiposDePago;
use App\Models\Empleado;
use App\Models\Categoria;

class VentasDetalladasSheet implements FromArray, WithTitle, WithStyles, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        
        $rows[] = ['Ventas Detalladas por Técnico'];
        $rows[] = ['Del ' . $this->data['startDate'] . ' al ' . $this->data['endDate']];
        $rows[] = []; 
        
        foreach ($this->data['ventasDetalladasPorTecnico'] as $tecnico) {
            $rows[] = [
                'Técnico',
                'Ventas',
                'Valor Total',
                'Pagado',
                'Ganancia'
            ];
            
            $rows[] = [
                $tecnico['tecnico'],
                count($tecnico['ventas']),
                $tecnico['total_ventas'],
                $tecnico['ventas']->sum('total_pagado'),
                $tecnico['ganancia_total']
            ];
            
            $rows[] = []; 
            
            $rows[] = [
                'ID Venta',
                'Fecha',
                'Cliente',
                'Valor',
                'Tipo',
                'Estatus',
                'Métodos Pago',
                'Pagado',
                'Ganancia'
            ];
            
            foreach ($tecnico['ventas'] as $venta) {
                $metodosPago = '';
                if (isset($venta['pagos'])) {
                    $metodos = array_unique(array_map(function($pago) {
                        return $pago['metodo_pago'] ?? '';
                    }, $venta['pagos']));
                    $metodosPago = implode(', ', $metodos);
                }
                
                $rows[] = [
                    '#' . $venta['id'],
                    \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y'),
                    $venta['cliente'],
                    $venta['valor_total'],
                    ucfirst($venta['tipo_venta']),
                    ucfirst($venta['estatus']),
                    $metodosPago ?: 'Sin pagos',
                    $venta['total_pagado'],
                    $venta['ganancia_bruta']
                ];
                
                if (!empty($venta['trabajos'])) {
                    $rows[] = ['TRABAJOS REALIZADOS:'];
                    foreach ($venta['trabajos'] as $trabajo) {
                        $rows[] = [
                            $trabajo['trabajo'],
                            $trabajo['descripcion'] ?? '',
                            '',
                            '$' . number_format($trabajo['precio_trabajo'], 2)
                        ];
                        
                        if (!empty($trabajo['productos'])) {
                            $rows[] = ['PRODUCTOS UTILIZADOS:'];
                            $rows[] = [
                                'Producto',
                                'Cantidad',
                                'Precio Unitario',
                                'Total'
                            ];
                            
                            foreach ($trabajo['productos'] as $producto) {
                                $rows[] = [
                                    $producto['nombre'],
                                    $producto['cantidad'],
                                    '$' . number_format($producto['precio'], 2),
                                    '$' . number_format($producto['cantidad'] * $producto['precio'], 2)
                                ];
                            }
                        }
                    }
                    $rows[] = []; 
                }
                
                if (!empty($venta['costos'])) {
                    $rows[] = ['COSTOS ASOCIADOS:'];
                    $rows[] = [
                        'ID',
                        'Descripción',
                        'Subcategoría',
                        'Método Pago',
                        'Monto'
                    ];
                    
                    foreach ($venta['costos'] as $costo) {
                        $rows[] = [
                            '#'.$costo['id'],
                            $costo['descripcion'],
                            $this->getSubcategoria($costo['subcategoria']),
                            $this->getMetodoPago($costo['metodo_pago_id']),
                            $costo['valor']
                        ];
                    }
                    $rows[] = ['TOTAL COSTOS:', '', '', '', $venta['total_costos']];
                    $rows[] = []; 
                }
                
                if (!empty($venta['gastos'])) {
                    $rows[] = ['GASTOS ASOCIADOS:'];
                    $rows[] = [
                        'ID',
                        'Descripción',
                        'Subcategoría',
                        'Método Pago',
                        'Monto'
                    ];
                    
                    foreach ($venta['gastos'] as $gasto) {
                        $rows[] = [
                            '#'.$gasto['id'],
                            $gasto['descripcion'],
                            $this->getSubcategoria($gasto['subcategoria']),
                            $this->getMetodoPago($gasto['metodo_pago_id']),
                            $gasto['valor']
                        ];
                    }
                    $rows[] = ['TOTAL GASTOS:', '', '', '', $venta['total_gastos']];
                    $rows[] = []; 
                }
                
                if (!empty($venta['pagos'])) {
                    $rows[] = ['DETALLE DE PAGOS:'];
                    $rows[] = [
                        'Fecha',
                        'Método Pago',
                        'Cobró',
                        'Monto'
                    ];
                    
                    foreach ($venta['pagos'] as $pago) {
                        $rows[] = [
                            \Carbon\Carbon::parse($pago['fecha'] ?? now())->format('d/m/Y'),
                            $this->getMetodoPago($pago['metodo_pago']),
                            $this->getNombreCobrador($pago['cobrador_id']),
                            $pago['monto']
                        ];
                    }
                    $rows[] = ['TOTAL PAGADO:', '', '', $venta['total_pagado']];
                }
                
                $rows[] = [];
            }
            
            $rows[] = [];
        }
        
        return $rows;
    }

    private function getMetodoPago($id)
    {
        if (!$id) return 'Sin especificar';
        
        $metodo = \App\Models\TiposDePago::find($id);
        return $metodo ? $metodo->name : 'Método no encontrado';
    }

    private function getNombreCobrador($id)
    {
        if (!$id) return 'Sin cobrador';
        
        $cobrador = \App\Models\Empleado::find($id);
        return $cobrador ? $cobrador->nombre : 'Cobrador no encontrado';
    }

    private function getSubcategoria($id)
    {
        if (!$id) return 'Sin subcategoría';
        
        $subcategoria = \App\Models\Categoria::find($id);
        return $subcategoria ? $subcategoria->nombre : 'Subcategoría no encontrada';
    }

    public function title(): string
    {
        return 'Ventas Detalladas';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '004080']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $highestRow = $sheet->getHighestRow();
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A'.$row)->getValue();

            if (in_array($cellValue, ['Técnico', 'ID Venta', 'TRABAJOS REALIZADOS:', 'COSTOS ASOCIADOS:', 
                                     'GASTOS ASOCIADOS:', 'DETALLE DE PAGOS:', 'PRODUCTOS UTILIZADOS:'])) {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '004080']
                    ]
                ]);
                
                if (in_array($cellValue, ['TRABAJOS REALIZADOS:', 'COSTOS ASOCIADOS:', 
                                        'GASTOS ASOCIADOS:', 'DETALLE DE PAGOS:', 'PRODUCTOS UTILIZADOS:'])) {
                    $sheet->mergeCells('A'.$row.':I'.$row);
                }
            }

            elseif (is_array($cellValue) || in_array($sheet->getCell('B'.$row)->getValue(), 
                   ['Descripción', 'Producto', 'Fecha', 'ID'])) {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F0F0F0']
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);
            }

            elseif (strpos($cellValue, 'TOTAL') === 0) {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFF9C4']
                    ]
                ]);
            }

            $gananciaCell = $sheet->getCell('I'.$row);
            if (is_numeric($gananciaCell->getValue()) && $gananciaCell->getValue() < 0) {
                $sheet->getStyle('I'.$row)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => 'FF0000']
                    ]
                ]);
            }
        }

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);

        $sheet->getStyle('D2:I'.$highestRow)
            ->getNumberFormat()
            ->setFormatCode('$#,##0.00');
    }

    public function columnFormats(): array
    {
        return [
            'D' => '"$"#,##0.00',
            'H' => '"$"#,##0.00',
            'I' => '"$"#,##0.00'
        ];
    }
}