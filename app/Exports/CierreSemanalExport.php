<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ResumenFinancieroSheet;
use App\Exports\Sheets\ResumenVentasSheet;
use App\Exports\Sheets\VentasDetalladasSheet;
use App\Exports\Sheets\CostosSheet;
use App\Exports\Sheets\LlavesSheet;
use App\Exports\Sheets\GastosSheet;
use App\Exports\Sheets\VentasPorLugarSheet;
use App\Exports\Sheets\VentasContadoPorTrabajoSheet;
use App\Exports\Sheets\VentasCreditoPorTrabajoSheet;
use App\Exports\Sheets\ResumenTrabajosSheet;
use App\Exports\Sheets\VentasPorClienteSheet;
use App\Exports\Sheets\VentasCreditoSheet;

class CierreSemanalExport implements WithMultipleSheets
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ResumenFinancieroSheet($this->data),
            new ResumenVentasSheet($this->data),
            new VentasDetalladasSheet($this->data),
            new CostosSheet($this->data),
            new GastosSheet($this->data),
            new LlavesSheet($this->data),
            new VentasPorLugarSheet($this->data),
            new VentasContadoPorTrabajoSheet($this->data),
            new VentasCreditoPorTrabajoSheet($this->data),
            new ResumenTrabajosSheet($this->data),
            new VentasPorClienteSheet($this->data)
        ];
    }
}